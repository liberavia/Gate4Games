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
    target_desktopfile.write('Name=' + title + ' (' + install_type + ')')
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
    
    if systemtype == 'psx':
        fileending = "bin"
        target_path = FOLDER_ROMS + "/psx/"
        target_filename = "rom_" + progress_id + "." + fileending
        target_scriptfile.write('#!/bin/sh')
        target_scriptfile.write("\n")
        target_scriptfile.write('qjoypad Playstation1 &')
        target_scriptfile.write("\n")
        target_scriptfile.write('killall -9 kodi.bin')
        target_scriptfile.write("\n")
        target_scriptfile.write('pcsx -nogui -cdfile ' + target_path + target_filename)
        target_scriptfile.write("\n")
        target_scriptfile.write('killall -9 pcsx')
        target_scriptfile.write("\n")
        target_scriptfile.write('killall -9 qjoypad')
        target_scriptfile.write("\n")
        target_scriptfile.write('/usr/bin/kodi -fs')
        target_scriptfile.write("\n")
        
        
    target_scriptfile.close()
    
    st = os.stat(script_filepath)
    os.chmod(script_filepath, st.st_mode | stat.S_IEXEC)
    


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