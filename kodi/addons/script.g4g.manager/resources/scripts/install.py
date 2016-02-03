#!/usr/bin/env python
# Script which is used to trigger an installation. Takes different arguments and will periodically pass its state into 
# progress file located in .g4g/progress/

import os
import sys
import subprocess
import time
import shutil
import stat
from os.path import expanduser
import ntpath
import re
import pprint
import xml.etree.ElementTree as ET
import urlparse
import urllib
import urllib2
import json
import zipfile
import getopt
#import xbmcaddon

# xbmc addon stuff
#addon = xbmcaddon.Addon(id='script.g4g.manager')
#language = addon.getLocalizedString



# std folders
HOME_DIR = expanduser("~")
FOLDER_G4G = os.path.join(HOME_DIR, '.g4g')
FOLDER_SCRIPTS = os.path.join(FOLDER_G4G, 'scripts')
FOLDER_APPS = os.path.join(FOLDER_G4G, 'applications')
FOLDER_IMAGES = os.path.join(FOLDER_G4G, 'images')
FOLDER_ICONS = os.path.join(FOLDER_IMAGES, 'icons')
FOLDER_FANART = os.path.join(FOLDER_IMAGES, 'fanart')
FOLDER_COVER = os.path.join(FOLDER_IMAGES, 'cover')
FOLDER_PROGRESS = os.path.join(FOLDER_G4G, 'progress')
FOLDER_DOWNLOADS = os.path.join(FOLDER_G4G, 'downloads')
FOLDER_ROMS = os.path.join(FOLDER_G4G, 'roms')
# std setttings
QJOYPAD_THEME_EXIT_ONLY = "Playstation1"

# get options set
opts, args = getopt.getopt(sys.argv[1:], 'd:p:a:u:n:i:s:f', ['downloadtype=', 'packagetype=', 'appid=', 'url=', 'name=', 'image=', 'systemtype=','fanart='])
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

# write current progress into progress file
def write_progress(percent, progress_id, name, message, image="", downloaded="", todownload="" ):
    progress_info = dict([('pid', str(os.getpid())), ('percent', percent), ('name', name), ('downloaded', downloaded), ('todownload', todownload), ('image', image), ('message', message)])
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


def direct_download(progress_id, source, name, image, message):
    # do the direct download
    target_path = FOLDER_DOWNLOADS + "/download_" + progress_id + ".zip"
    urllib.urlretrieve(source,target_path,lambda nb, bs, fs, url=source: direct_download_progress_message(nb,bs,fs,progress_id,name,image,message))

    
def extract_package(progress_id, name, image, message, systemtype):
    write_progress(10,progress_id, name, message, image)
    message = "Extracting " + name
    if systemtype == 'psx':
        fileending = "bin"
        download_source = FOLDER_DOWNLOADS + "/download_" + progress_id + ".zip"
        target_path = FOLDER_ROMS + "/psx/"
        target_filename = "rom_" + progress_id + "." + fileending
    elif systemtype == 'nintendo_gamecube':
        fileending = "iso"
        download_source = FOLDER_DOWNLOADS + "/download_" + progress_id + ".zip"
        target_path = FOLDER_ROMS + "/gamecube/"
        target_filename = "rom_" + progress_id + "." + fileending
    
    fh = open(download_source, 'rb')
    downloaded_zip = zipfile.ZipFile(fh)
    
    # search file
    for filename in downloaded_zip.namelist():
        splitted_filename = filename.split('.')
        file_ending = splitted_filename[1]
        if file_ending == fileending:
            write_progress(50,progress_id, name, message, image)
            downloaded_zip.extract(filename, target_path)
            downloaded_zipfilename = filename
    fh.close()
    
    write_progress(95,progress_id, name, message, image)
    os.rename(target_path + downloaded_zipfilename, target_path + target_filename)
    os.remove(download_source)
    
    
def direct_download_progress_message(numblocks, blocksize, filesize, progress_id, name, image, message):
    percent = min((numblocks*blocksize*100)/filesize, 100)
    todownload = str(numblocks*blocksize)
    downloaded = str(filesize)
    write_progress(percent, progress_id, name, message, image, downloaded, todownload )    

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
    }
    
    return switcher.get(systemtype, "none")


# returns subfolder by given systemtype
def get_subfolder_by_systemtype(systemtype):
    switcher = {
        'psx'                   : 'psx',
        'nintendo_gamecube'     : 'gamecube',
    }
    
    return switcher.get(systemtype, "none")


# returns start command by given systemtype
def get_startcommand_by_systemtype(systemtype, target_path, target_filename):
    switcher = {
        'psx'                   : 'pcsx -nogui -cdfile ' + target_path + target_filename,
        'nintendo_gamecube'     : 'dolphin-emu --exec="'+ target_path + target_filename + '" --batch &',
    }
    
    return switcher.get(systemtype, "")


# returns subfolder by given systemtype
def get_program_by_systemtype(systemtype):
    switcher = {
        'psx'                   : 'pcsx',
        'nintendo_gamecube'     : '',
    }
    
    return switcher.get(systemtype, "")


# returns matching qjoypad load line
def get_qjoypad_theme_by_systemtype(systemtype,progress_id):
    # currently just return the standard exit
    theme_line = ""
    if systemtype == 'psx' or systemtype == 'nintendo_gamecube':
        theme_line += "qjoypad " + QJOYPAD_THEME_EXIT_ONLY + " &\n"
    
    return theme_line

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
    content += get_qjoypad_theme_by_systemtype(systemtype,progress_id)
    content += "killall -9 kodi.bin\n"
    content += "sc-desktop.py stop\n"
    content += "sc-xbox.py start\n"
    content += get_startcommand_by_systemtype(systemtype, target_path, target_filename) + "\n"
    content += get_xdotool_by_systemtype(systemtype)
    content += "sc-xbox.py stop\n"
    content += "sc-desktop.py start\n"
    content += get_qjoypad_kill_by_systemtype(systemtype,progress_id)
    content += "/usr/bin/kodi -fs &\n"
    
    return content

# MAIN PROGRAM
progress_id = get_next_game_id()
progress_filename = "/progress_" + progress_id + ".json"

try:
    fanart
except:
    fanart=image

if downloadtype == 'direct':
    message = "Downloading " + name
    direct_download(progress_id, url, name, image, message)

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