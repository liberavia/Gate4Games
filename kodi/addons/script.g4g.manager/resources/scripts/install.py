#!/usr/bin/env python
# Script which is used to trigger an installation. Takes different arguments and will periodically pass its state into 
# progress file located in .g4g/progress/

import os
import sys
import subprocess
import time
import shutil
import stat
import ntpath
import re
import pprint
import urlparse
import urllib
import urllib2
import json
import zipfile
import getopt
import PyVDF
import glob
import pexpect
import hashlib
import thread
import threading
import xml.etree.ElementTree as ET
from os.path import expanduser
from pySmartDL import SmartDL

#import xbmcaddon

# xbmc addon stuff
#addon = xbmcaddon.Addon(id='script.g4g.manager')
#language = addon.getLocalizedString



# std folders
HOME_DIR = expanduser("~")
FOLDER_QJOYPAD = os.path.join(HOME_DIR, '.qjoypad3')
FOLDER_G4G = os.path.join(HOME_DIR, '.g4g')
FOLDER_G4G_STEAM = os.path.join(FOLDER_G4G, 'steam')
FOLDER_G4G_STEAM_CACHE = os.path.join(FOLDER_G4G_STEAM, 'cache')
FOLDER_STEAMCMD = os.path.join(HOME_DIR, '.steamcmd')
FOLDER_SCRIPTS = os.path.join(FOLDER_G4G, 'scripts')
FOLDER_APPS = os.path.join(FOLDER_G4G, 'applications')
FOLDER_IMAGES = os.path.join(FOLDER_G4G, 'images')
FOLDER_ICONS = os.path.join(FOLDER_IMAGES, 'icons')
FOLDER_FANART = os.path.join(FOLDER_IMAGES, 'fanart')
FOLDER_COVER = os.path.join(FOLDER_IMAGES, 'cover')
FOLDER_PROGRESS = os.path.join(FOLDER_G4G, 'progress')
FOLDER_DOWNLOADS = os.path.join(FOLDER_G4G, 'downloads')
FOLDER_ROMS = os.path.join(FOLDER_G4G, 'roms')
FOLDER_SCRIPTCONFIGS = os.path.join(FOLDER_G4G, 'scriptconfigs')
FOLDER_CONTROLLER = os.path.join(FOLDER_SCRIPTCONFIGS, 'controller')
FOLDER_CONTROLLER_DEFAULTS = os.path.join(FOLDER_SCRIPTCONFIGS, 'controller', 'default')
FOLDER_TEMP = os.path.join(FOLDER_G4G, 'temp')


FOLDER_GAME_MANAGER_SCRIPTS = os.path.join(HOME_DIR, '.kodi', 'addons', 'script.g4g.manager', 'resources', 'scripts')

# std setttings
QJOYPAD_THEME_EXIT_ONLY = "OverlayTrigger"
QJOYPAD_LAYOUT_PSX = "OverlayTrigger"
QJOYPAD_LAYOUT_GAMECUBE = "OverlayTrigger"
QJOYPAD_LAYOUT_NES = "NES"
QJOYPAD_LAYOUT_N64 = "OverlayTrigger"

subpid = ""


# get options set
opts, args = getopt.getopt(sys.argv[1:], 'd:p:a:u:n:i:s:f:l:p:c', ['downloadtype=', 'packagetype=', 'appid=', 'url=', 'name=', 'image=', 'systemtype=','fanart=','login=','password=','catalog='])
pprint.pprint(opts)

for opt, arg in opts:
    if opt in ('-t', '--downloadtype'):
        downloadtype = arg
    elif opt in ('-p', '--packagetype'):
        packagetype = arg
    elif opt in ('-a', '--appid'):
        appid = arg
    elif opt in ('-u', '--url'):
        url = arg
    elif opt in ('-n', '--name'):
        name = arg
    elif opt in ('-i', '--image'):
        image = arg
    elif opt in ('-s', '--systemtype'):
        systemtype = arg
    elif opt in ('-f', '--fanart'):
        fanart = arg
    elif opt in ('-l', '--login'):
        login = arg
    elif opt in ('-p', '--password'):
        password = arg
    elif opt in ('-c', '--catalog'):
        folder = arg


# write current progress into progress file
def write_progress(percent, progress_id, name, message, image="", downloaded="", todownload="", remainingtime="", currentrate=""):
    global subpid
    progress_info = dict([('pid', str(os.getpid())), ('percent', percent), ('name', name), ('downloaded', downloaded), ('todownload', todownload), ('image', image), ('message', message), ('remainingtime', remainingtime), ('currentrate', currentrate), ('subpid', subpid)])
    print progress_info
    progress_info_json = json.dumps(progress_info)
    progress_filename = "progress_" + progress_id + ".json"
    filehandler = open(FOLDER_PROGRESS + "/" + progress_filename, 'w')
    filehandler.write(progress_info_json)
    filehandler.close()
    
    
# checks application dir iterate through all files and take the highest value add 1 and transfer it to 8 letter length string 
def get_next_game_id():
    highest_id = 0
    
    for filename in os.listdir(FOLDER_SCRIPTS):
        splitted_filename = filename.split("_")
        current_id = splitted_filename[1]
        id_value = int(current_id)
        if id_value > highest_id:
            highest_id = id_value
    for filename in os.listdir(FOLDER_DOWNLOADS):
        highest_id += 1
    for filename in os.listdir(FOLDER_PROGRESS):
        highest_id += 1
        
    highest_id += 1
    next_id = str(highest_id)
    next_id = next_id.zfill(8)
    
    return next_id

# starts a download via steamcmd and does pol setup linked to gateos wine-steam-install
def wine_steam_download(progress_id, appid, name, image, message, login, password, folder):
    steam_user          = login.encode('utf8') 
    steam_user_hash     = get_hash_of(login)
    steam_app_folder    = folder.encode('utf8')
    install_method      = 'wine_steam'

    # init values
    write_progress("0", progress_id, name, message, image, "0", "100", "", "")
    
    print "VOR THREAD *******************************************************"
    # trigger thread for steamcmd commmand
    trigger_download        = threading.Thread(target=start_wine_steam_download, args=(appid, login, password, steam_app_folder))
    trigger_download.daemon = True
    trigger_download.start()    

    print "NACH THREAD *******************************************************"
    # wait a moment so thread can do the initialization
    time.sleep(3)
    
    message_output_file = get_message_output_file(steam_user, appid, install_method)
    message_file_path   = os.path.join(FOLDER_TEMP,message_output_file)
    
    while os.path.isfile(message_file_path):
        print "Hole Message von "+ message_file_path + "+++++++++++++++++++++++++++++"
        latest_message  = get_latest_message(message_file_path)
        parsed_message  = parse_latest_message(latest_message, install_method)
        downloaded      = parsed_message['downloaded']
        todownload      = parsed_message['todownload']
        remainingtime   = "-"
        currentrate     = "-"
        percent         = parsed_message['percent']
        write_progress(percent, progress_id, name, message, image, downloaded, todownload, remainingtime, currentrate)
        time.sleep(1)


# parse string for pattern and return list of values
def parse_latest_message(latest_message, install_method):
    pattern = "downloading, progress: ([0-9,]+) \(([0-9]+) \/ ([0-9]+)\)"
    matches = re.findall(pattern,latest_message,re.DOTALL)
    try:
        matches = matches[0]
    except:
        pass
    pprint.pprint(matches)
    
    #percent
    try:
        percent_raw             = matches[0]
        print percent_raw
        percent_float           = float(percent_raw.replace(",","."))
        percent                 = int(round(percent_float,0))
    except:
        percent                 = "0"

    #downloaed
    try:
        downloaded_bytes        = int(matches[1])
        print downloaded_bytes
        downloaded_kbytes       = float(downloaded_bytes/1024)
        downloaded_mbytes       = float(downloaded_kbytes/1024)
        downloaded              = round(downloaded_mbytes,2)
        downloaded              = str(downloaded)
        downloaded              = downloaded.replace('.',',')
    except:
        downloaded              = "0"

    #todownload
    try:
        todownload_bytes        = int(matches[2])
        print todownload_bytes
        todownload_kbytes       = float(todownload_bytes/1024)
        todownload_mbytes       = float(todownload_kbytes/1024)
        todownload              = round(todownload_mbytes,2)
        todownload              = str(todownload)
        todownload              = todownload.replace('.',',')
    except:
        todownload              = "0"
    
    
    parsed_result = {"percent" : percent, "downloaded":downloaded, "todownload":todownload}
    
    print parsed_result
    
    return parsed_result

# get latest message of delivered message_file
def get_latest_message(message_file_path):
    message_file_handler    = open(message_file_path,'r')
    latest_message          = tail(message_file_handler,1)
    message_file_handler.close()
    
    print latest_message[0]
    
    return latest_message[0]


# Reads a n lines from f with an offset of offset lines
def tail(f, n, offset=0):    
    avg_line_length = 74
    to_read = n + offset
    while 1:
        try:
            f.seek(-(avg_line_length * to_read), 2)
        except IOError:
            # woops.  apparently file is smaller than what we want
            # to step back, go to the beginning instead
            f.seek(0)
        pos = f.tell()
        lines = f.read().splitlines()
        if len(lines) >= to_read or pos == 0:
            return lines[-to_read:offset and -offset or None]
        avg_line_length *= 1.3
        
# returns a message output filename generated by user and an id
def get_message_output_file(user,gameid,installtype):
    game_hash           = get_hash_of(user + gameid)
    message_output_file = installtype + '_' + game_hash + '.txt'
    
    return message_output_file
    

# starts a steam download as a thread
def start_wine_steam_download(appid, login, password, folder):
    global subpid
    message_output_file = get_message_output_file(login, appid, 'wine_steam')
    message_file_path   = os.path.join(FOLDER_TEMP,message_output_file)
    steamcmd_command    = os.path.join(FOLDER_STEAMCMD, 'steamcmd.sh')
    steam_user_hash     = get_hash_of(login)
    appid_vdf_filename  = steam_user_hash + '_' + str(appid) + '.vdf'
    app_vdf_filepath    = os.path.join(FOLDER_G4G_STEAM_CACHE,appid_vdf_filename)
    print app_vdf_filepath
    VdfAppFile          = PyVDF()
    fh                  = open(app_vdf_filepath,'r')
    AppFileContent      = fh.read()
    AppFileContent      = AppFileContent.replace('\\','')
    fh.close()
    VdfAppFile.setMaxTokenLength(4096)
    VdfAppFile.loads(AppFileContent)
    InstallDir          = VdfAppFile.find(appid + ".config.installdir")
    InstallDir          = str(InstallDir)
    folder              = os.path.join(HOME_DIR, folder)
    force_install_dir   = folder + "steamapps/common/" + InstallDir.encode('utf8')
    try:
        os.makedirs(force_install_dir, 0755)
    except:
        pass
        
    # build command
    steam_command_opts  = ' ' + '+@sSteamCmdForcePlatformType windows +login ' + login + ' ' + password + ' + force_install_dir "' +  force_install_dir + '"'
    steam_start_command = "unbuffer " + steamcmd_command + steam_command_opts + " +app_update " + appid + " validate +quit" + " > " + message_file_path
    
    subpid              = subprocess.Popen(steam_start_command, shell=True, close_fds=True).pid


# returns md5 hash of ingoing string
def get_hash_of(ingoing):
    #create hash of user so we always will have the right user library
    m = hashlib.md5()
    m.update(ingoing)
    outgoing = m.hexdigest()
    
    return str(outgoing)
        

def direct_download(progress_id, source, name, image, message):
    # do the direct download
    target_path = FOLDER_DOWNLOADS + "/download_" + progress_id + ".zip"
    download = SmartDL(source, target_path, progress_bar=False)
    download.start(blocking=False)
    while not download.isFinished():
        currentrate     = str(download.get_speed(human=True))
        remaining_eta   = int(download.get_eta())
        remaining_hrs   = str(int(remaining_eta / 3600))
        remaining_hrs   = remaining_hrs.zfill(2)
        remaining_min   = str(int(remaining_eta / 60))
        remaining_min   = remaining_min.zfill(2)
        remaining_sec   = str(int((remaining_eta % 60) % 60))
        remaining_sec   = remaining_sec.zfill(2)
        remainingtime   = remaining_hrs + ":" + remaining_min + ":" + remaining_sec
        percent         = str(int((download.get_progress()*100)))
        downloaded      = str(download.get_dl_size(human=True))
        todownload      = ""
        
        write_progress(percent, progress_id, name, message, image, downloaded, todownload, remainingtime, currentrate)
        time.sleep(1)
    
    
def extract_package(progress_id, name, image, message, systemtype):
    write_progress(10,progress_id, name, message, image)
    message = "Extracting " + name
    download_source = FOLDER_DOWNLOADS + "/download_" + progress_id + ".zip"
    fileending = get_fileending_by_systemtype(systemtype)
    target_path = os.path.join(FOLDER_ROMS, get_subfolder_by_systemtype(systemtype)) + "/"
    target_filename = "rom_" + progress_id + "." + fileending
    
    fh = open(download_source, 'rb')
    downloaded_zip = zipfile.ZipFile(fh)
    
    # search file
    for filename in downloaded_zip.namelist():
        splitted_filename = filename.split('.')
        repr(splitted_filename)
        file_ending = splitted_filename[1]
        if file_ending.lower() == fileending.lower():
            write_progress(50,progress_id, name, message, image)
            #zipfile_obj = downloaded_zip.open(filename)
            #shutil.copyfileobj(zipfile_obj, os.path.join(target_path,target_filename))
            downloaded_zip.extract(filename, target_path)
            downloaded_zipfilename = filename
    fh.close()
    
    write_progress(95,progress_id, name, message, image)
    if downloaded_zipfilename != None:
        os.rename(target_path + downloaded_zipfilename, target_path + target_filename)
    os.remove(download_source)
    
    
#def direct_download_progress_message(numblocks, blocksize, filesize, progress_id, name, image, message):
    #percent = min((numblocks*blocksize*100)/filesize, 100)
    #todownload = str(numblocks*blocksize)
    #downloaded = str(filesize)
    #write_progress(percent, progress_id, name, message, image, downloaded, todownload )    

# sends a notification to kodi instance
def send_notification(header,message,length,image):
    #kodi-send -a "Notification(My header,This is my message,5000,http://www.gate4games.com/out/pictures/generated/product/1/560_315_75/nopic.jpg)"
    cmd = 'kodi-send -a "Notification('+header+','+message+','+str(length)+","+image+')"'
    #cmd = '"%s"' % (cmd)
    subprocess.Popen(cmd, shell=True, close_fds=True)

def finish_install(progress_id, name, image, fanart, message, systemtype, pc_type=""):
    message = "Finish installation"
    create_script(progress_id, name, image, message, systemtype)
    write_progress(40, progress_id, name, message, image)
    time.sleep(1)
    create_desktop_file(name,systemtype,progress_id,image,fanart,pc_type)
    write_progress(80, progress_id, name, message, image)
    time.sleep(1)
    textures_db_path = HOME_DIR + "/.kodi/userdata/Database/Textures13.db"
    if os.path.isfile(textures_db_path):
        os.remove(textures_db_path)
    write_progress(90, progress_id, name, message, image)
    time.sleep(1)
    
# creating a desktop file to make library noticed about the installation    
def create_desktop_file(title,install_type,next_id,thumbnail,fanart,pc_type):
    script_filepath     = FOLDER_SCRIPTS + "/script_" + next_id
    desktop_filepath    = FOLDER_APPS + "/game_" + next_id + ".desktop"
    icon_filepath       = FOLDER_ICONS + "/icon_" + next_id + ".jpg"
    cover_filepath      = FOLDER_COVER + "/cover_" + next_id + ".jpg"
    fanart_filepath     = FOLDER_FANART + "/fanart_" + next_id + ".jpg"
    
    target_desktopfile = open(desktop_filepath, 'w')
    
    target_desktopfile.write('[Desktop Entry]')
    target_desktopfile.write("\n")
    target_desktopfile.write('Version=1.0')
    target_desktopfile.write("\n")
    target_desktopfile.write('Terminal=false')
    target_desktopfile.write("\n")
    target_desktopfile.write('Type='+ install_type)
    target_desktopfile.write("\n")
    target_desktopfile.write('PCType='+ pc_type)
    target_desktopfile.write("\n")
    target_desktopfile.write('Name=' + title )
    target_desktopfile.write("\n")
    target_desktopfile.write('Exec=' + script_filepath)
    target_desktopfile.write("\n")
    target_desktopfile.write('Icon=' + icon_filepath)
    target_desktopfile.close()
    
    urllib.urlretrieve(thumbnail, cover_filepath)    
    urllib.urlretrieve(fanart, fanart_filepath)
    
# create an executable startscript    
def create_script(progress_id, name, image, message, systemtype):
    script_filepath = FOLDER_SCRIPTS + "/script_" + progress_id
    target_scriptfile = open(script_filepath, 'w')
    
    subfolder = get_subfolder_by_systemtype(systemtype)
    fileending = get_fileending_by_systemtype(systemtype)
    
    target_path = FOLDER_ROMS + "/" + subfolder + "/"
    target_filename = "rom_" + progress_id + "." + fileending
    
    filecontent = get_script_content(systemtype, progress_id,target_path,target_filename)
    
    target_scriptfile.write(filecontent)
    target_scriptfile.close()
    st = os.stat(script_filepath)
    os.chmod(script_filepath, st.st_mode | stat.S_IEXEC)
        
    
# return fileending by given systemtype
def get_fileending_by_systemtype(systemtype):
    switcher = {
        'psx'                   : 'bin',
        'nintendo_gamecube'     : 'iso',
        'nes'                   : 'nes',
        'n64'                   : 'v64',
    }
    
    return switcher.get(systemtype, "none")


# returns subfolder by given systemtype
def get_subfolder_by_systemtype(systemtype):
    switcher = {
        'psx'                   : 'psx',
        'nintendo_gamecube'     : 'gamecube',
        'nes'                   : 'nes',
        'n64'                   : 'n64',
    }
    
    return switcher.get(systemtype, "none")


# returns start command by given systemtype
def get_startcommand_by_systemtype(systemtype, target_path, target_filename):
    switcher = {
        'psx'                   : 'pcsx -nogui -cdfile ' + target_path + target_filename + ' &',
        'nintendo_gamecube'     : 'dolphin-emu --exec="'+ target_path + target_filename + '" --batch &',
        'nes'                   : 'fceux --nogui -fs 1 "' + target_path + target_filename + '" &',
        'n64'                   : 'mupen64plus --fullscreen --noosd --resolution 1920x1080 "' + target_path + target_filename + '" &',
    }
    
    return switcher.get(systemtype, "")


# returns subfolder by given systemtype
def get_program_by_systemtype(systemtype):
    switcher = {
        'psx'                   : 'pcsx',
        'nintendo_gamecube'     : '',
    }
    
    return switcher.get(systemtype, "")


# returns the layout for the game installed
# @todo: game possibly needs to deliver its own default config
def get_qjoypad_default_layout(systemtype,extrainfos):
    fallback = os.path.join(FOLDER_QJOYPAD, QJOYPAD_THEME_EXIT_ONLY + '.lyt')
    progress_id = extrainfos.get('progress_id')
    switcher = {
        'psx'                   : os.path.join(FOLDER_QJOYPAD, QJOYPAD_LAYOUT_PSX + '.lyt'),
        'nintendo_gamecube'     : os.path.join(FOLDER_QJOYPAD, QJOYPAD_LAYOUT_GAMECUBE + '.lyt'),
        'nes'                   : os.path.join(FOLDER_QJOYPAD, QJOYPAD_LAYOUT_NES + '.lyt'),
        'n64'                   : os.path.join(FOLDER_QJOYPAD, QJOYPAD_LAYOUT_N64 + '.lyt'),
        'pc'                    : os.path.join(FOLDER_CONTROLLER, progress_id, 'layout_' + progress_id + '.lyt')
    }
    
    return switcher.get(systemtype, fallback)
    

# returns matching qjoypad load line
def get_qjoypad_layout_by_systemtype(systemtype,progress_id):
    extrainfos = {'progress_id':progress_id}
    layout_line = ""
    sourcepath = get_qjoypad_default_layout(systemtype,extrainfos)
    
    # copy layout to qjoypad folder
    dest_layout= 'layout_' + progress_id
    dest_layout_filename = dest_layout + '.lyt'
    destpath = os.path.join(FOLDER_QJOYPAD,dest_layout_filename)
    
    if not os.path.isfile(destpath):
        try:
            shutil.copyfile(sourcepath,destpath)
        except:
            pass
    
    layout_line += "qjoypad " + dest_layout + " &\n"
    
    return layout_line

# returns matching qjoypad kill line
def get_qjoypad_kill_by_systemtype(systemtype,progress_id):
    # currently just return the standard exit
    kill_line = ""
    if systemtype == 'psx' or systemtype == 'nintendo_gamecube':
        kill_line += "killall -9 qjoypad\n"
    
    return kill_line

# create xdotool
def get_xdotool_by_systemtype(systemtype):
    xdotool_script = ""
    if systemtype == 'nintendo_gamecube':
        xdotool_script += "xprop_command=`which xprop`\n"
        xdotool_script += "xdotool_command=`which xdotool`\n"
        xdotool_script += "xte_command=`which xte`\n"
        xdotool_script += "PID=$!\n"
        xdotool_script += "sleep 2\n"
        xdotool_script += "$xdotool_command windowminimize $(xdotool getactivewindow)\n"
        xdotool_script += "sleep 1\n"
        xdotool_script += "wait $PID\n"
        
    return xdotool_script


# return script content by systemtype    
def get_script_content(systemtype, progress_id,target_path,target_filename):
    content  = "#!/bin/sh\n"
    content += get_qjoypad_layout_by_systemtype(systemtype,progress_id)
    content += "killall -9 kodi.bin\n"
    content += "sc-desktop.py stop\n"
    content += "sc-xbox.py start\n"
    content += get_script_start_overlay_command(progress_id) + "\n"
    content += "PID_OL_TRIGGER=$!\n"
    content += get_startcommand_by_systemtype(systemtype, target_path, target_filename) + "\n"
    content += get_xdotool_by_systemtype(systemtype)
    content += "until PID=`pgrep " + get_process_name_by_systemtype(progress_id, systemtype) + "`\n"
    content += "do\n"
    content += "\tsleep 1\n"
    content += "done\n"
    content += "echo $PID > " + os.path.join(FOLDER_G4G , 'temp', 'game.pid') + "\n"
    content += "while ps -p$PID > /dev/null; do sleep 1; done\n"
    content += "sc-xbox.py stop\n"
    content += "sc-desktop.py start\n"
    content += get_qjoypad_kill_by_systemtype(systemtype,progress_id)
    content += "kill -9 $PID_OL_TRIGGER\n"
    content += "rm " + os.path.join(FOLDER_G4G , 'temp', 'overlay.pid') + "\n"
    content += "/usr/bin/kodi -fs &\n"
    
    return content

# returns overlay_command
def get_script_start_overlay_command(progress_id):
    #/home/kbox/.kodi/addons/script.g4g.manager/resources/scripts/overlay_trigger.py &
    content  = ""
    content += os.path.join(FOLDER_GAME_MANAGER_SCRIPTS, 'overlay_trigger.py') + " &"
    
    return content

# returns process name
# @todo: currently only consoles due their process names are easy but it will be nessessary to provide some information in a config file in scriptconfigs
def get_process_name_by_systemtype(progress_id, systemtype):
    content  = ""
    if systemtype == 'psx':
        content += "pcsx"
    elif systemtype == 'nintendo_gamecube':
        content += "dolphin-emu"
    elif systemtype == 'nes':
        content += "fceux"
    elif systemtype == 'n64':
        content += "mupen64plus"
        
    return content


# MAIN PROGRAM
progress_id = get_next_game_id()
progress_filename = "/progress_" + progress_id + ".json"

try:
    packagetype
except:
    packagetype=""

try:
    fanart
except:
    fanart=image

if downloadtype == 'direct':
    message = "Downloading " + name
    direct_download(progress_id, url, name, image, message)

if downloadtype == 'wine_steam':
    message = "Downloading " + name
    wine_steam_download(progress_id, appid, name, image, message, login, password, folder)
    
if packagetype == 'zip':
    message = "Extracting " + name
    extract_package(progress_id, name, image, message, systemtype)

message = "Finishing installation"    
finish_install(progress_id, name, image, fanart, message, systemtype)    

message = name + " successfully installed"    
write_progress(100, progress_id, name, message, image)
time.sleep(2)
# send notification that installation finished 
# todo: need translation here
send_notification(name, message,5000,image)

#remove progress file when done

os.remove(FOLDER_PROGRESS + progress_filename)