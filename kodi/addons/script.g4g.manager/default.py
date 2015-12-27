# gate4games game manager
# script for managing videogames (download, install, start, remove). Provides different platforms in one interface

import os
import sys
import plugintools
import subprocess
import time
import shutil
import stat
import xbmc
import xbmcaddon
import xbmcgui
from os.path import expanduser
import ntpath
import re
import pprint
import xml.etree.ElementTree as ET
import urllib

plugintools.module_log_enabled = False
plugintools.http_debug_log_enabled = False

HOME_DIR = expanduser("~")
THUMBNAIL_PATH = os.path.join(plugintools.get_runtime_path() , "resources" , "img")
FANART = os.path.join(plugintools.get_runtime_path() , "fanart.jpg")
FANART = FANART.encode('utf8')
DEFAULT_THUMB = os.path.join(THUMBNAIL_PATH,"default.png").encode('utf-8')
SETTINGS_THUMB = os.path.join(THUMBNAIL_PATH,"settings.png").encode('utf-8')
FOLDER_G4G = os.path.join(HOME_DIR, '.g4g')
FOLDER_SCRIPTS = os.path.join(FOLDER_G4G, 'scripts')
FOLDER_APPS = os.path.join(FOLDER_G4G, 'applications')
FOLDER_IMAGES = os.path.join(FOLDER_G4G, 'images')
FOLDER_ICONS = os.path.join(FOLDER_IMAGES, 'icons')
FOLDER_FANART = os.path.join(FOLDER_IMAGES, 'fanart')
FOLDER_COVER = os.path.join(FOLDER_IMAGES, 'cover')

addon = xbmcaddon.Addon(id='script.g4g.manager')
addonPath = addon.getAddonInfo('path')
addonIcon = addon.getAddonInfo('icon')
addonVersion = addon.getAddonInfo('version')
dialog = xbmcgui.Dialog()
language = addon.getLocalizedString
scriptid = 'script.g4g.manager'
txt_encode = 'utf-8'

def log(msg):
    msg = msg.encode(txt_encode)
    xbmc.log('%s: %s' % (scriptid, msg))


def getAddonInstallPath():
    path = addon.getAddonInfo('path').decode("utf-8")
    return path


def getAddonDataPath():
    path = xbmc.translatePath('special://profile/addon_data/%s' % scriptid).decode("utf-8")
    if not os.path.exists(path):
        log('addon userdata folder does not exist, creating: %s' % path)
        try:
            os.makedirs(path)
            log('created directory: %s' % path)
        except:
            log('ERROR: failed to create directory: %s' % path)
            dialog.notification(language(50123), language(50126), addonIcon, 5000)
    return path
    
# Entry point
def run():
    log("g4gmanager.run")

    # Get params
    params = plugintools.get_params()

    if params.get("action") is None:
        main_list(params)
    else:
        action = params.get("action")
        exec action+"(params)"

    plugintools.close_item_list()
    
# Main menu
def main_list(params):
    log("g4gmanager.main_list "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)
    
    plugintools.add_item( action="dummy", title=language(50001).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="library_selection", title=language(50002).encode('utf-8') , thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="add_games", title=language(50003).encode('utf-8') , thumbnail=DEFAULT_THUMB , fanart=FANART , folder=True )
    plugintools.add_item( action="settings", title=language(50004).encode('utf-8') , thumbnail=SETTINGS_THUMB , fanart=FANART , folder=False )
    
# dummy message
def dummy(params):
    plugintools.message("Gate4Games Manager", "This feature ist currently not implemented.","")

# add games
def add_games(params):
    log("g4gmanager.add_games "+repr(params))
    
    plugintools.set_view(plugintools.THUMBNAIL)
    
    plugintools.add_item( action="add_pc_games", title=language(50020).encode('utf-8'), thumbnail = DEFAULT_THUMB, fanart=FANART , folder=True, page="1" )
    plugintools.add_item( action="dummy", title=language(50021).encode('utf-8') , thumbnail = DEFAULT_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="dummy", title=language(50022).encode('utf-8') , thumbnail = DEFAULT_THUMB , fanart=FANART , folder=False )
    plugintools.add_item( action="dummy", title=language(50023).encode('utf-8') , thumbnail = DEFAULT_THUMB , fanart=FANART , folder=False )
    plugintools.add_item( action="dummy", title=language(50024).encode('utf-8') , thumbnail = DEFAULT_THUMB , fanart=FANART , folder=False )

# add games
def add_pc_games(params):
    log("g4gmanager.add_pc_games "+repr(params))
    
    page = params.get('page') 

    extra = params.get('extra')
    
    # category filter?
    add_genre = ""
    if extra != '' and extra['genre'] != None and extra['genre'] != '':
        add_genre = "|GameGenre--" + urllib.quote_plus(extra['genre'])
            
    add_sortby = ""
    if extra != '' and extra['sortby'] != None and extra['sortby'] != '':
        add_sortby = "&sortby=" + urllib.quote_plus(extra['sortby'])
            
    add_sortdir = ""
    if extra != '' and extra['sortdir'] != None and extra['sortdir'] != '':
        add_sortdir = "&sortdir=" + urllib.quote_plus(extra['sortdir'])
        
    attributes_filter = "&attributes=CompatibilityTypeLin--Ja" + add_genre
    
    
    url = "http://www.gate4games.com/index.php?cl=gateosapi" + attributes_filter + add_sortby + add_sortdir
    
    if page > 1:
        url += "&page=" + str(page)
    log("g4gmanager.add_pc_games.url is "+ url)
    
    body,response_headers = plugintools.read_body_and_headers(url)
    
    root = ET.fromstring(body)
    log("g4gmanager.root "+root.tag)
    # products = root.findall("./products")
    
    # get list information
    for listinfos in root.iter('listinfos'):
        maxpage = listinfos.find('maxpage').text
    
    plugintools.set_view(plugintools.LIST)
    
    # add maintenance entries
    plugintools.add_item( action="add_pc_game_to_page", title=language(50050).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=extra, folder=True )
    plugintools.add_item( action="add_pc_game_by_genre", title=language(50051).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=extra, folder=True )
    plugintools.add_item( action="add_pc_game_sort", title=language(50052).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=extra, folder=True )

    for product in root.iter('product'):
        log("g4gmanager.product "+repr(product))
        product_id = product.find('id').text
        name = product.find('name').text
        currency = product.find('currency').text
        fromprice = product.find('fromprice').text
        fromprice = float(fromprice)
        fromprice = "{:10.2f}".format(fromprice)
        fromprice = str(fromprice)
        fromprice = fromprice.replace('.', ',')
        coverpic = product.find('coverpic').text
        
        title = name.encode('utf-8') + " " + language(50200).encode('utf-8') + fromprice + " " + currency
        
        plugintools.add_item( action="add_pc_game", title=title, thumbnail=coverpic, fanart=coverpic , extra=product_id, folder=True )
        
    # add prev and next page entry if there are still pages
    log("g4gmanager.current_page "+str(page)+ " maxpage " + str(maxpage))
    if int(page) < int(maxpage):
        next_page = int(page) + 1
        next_page_title = "[COLOR blue]" + language(50053).encode('utf-8') + " " +  str(next_page) + " " + language(50201).encode('utf-8') + " " + str(maxpage) + "[/COLOR]"
        plugintools.add_item( action="add_pc_games", title=next_page_title, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=extra, folder=True, page=str(next_page)  )

# directly jumping to certain page        
def add_pc_game_to_page(params):
    plugintools.set_view(plugintools.LIST)

# get genre filtered list
def add_pc_game_by_genre(params):
    plugintools.set_view(plugintools.LIST)

# get sorted list
def add_pc_game_sort(params):
    plugintools.set_view(plugintools.LIST)

# game details page        
def add_pc_game(params):
    plugintools.set_view(plugintools.LIST)
    
    #plugintools.add_item( action="dummy", title=params.get('title') , thumbnail=params.get('thumbnail'), fanart=params.get('fanrart') , folder=False )

# library selections    
def library_selection(params):
    log("g4gmanager.library_selection "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)
    
    plugintools.add_item( action="library_installed", title=language(50010).encode('utf-8') , thumbnail = DEFAULT_THUMB , fanart=FANART , folder=True )
    plugintools.add_item( action="dummy", title=language(50011).encode('utf-8') , thumbnail = DEFAULT_THUMB , fanart=FANART , folder=False )
    
# show installed apps and read their information
def library_installed(params):
    for app_file in os.listdir(FOLDER_APPS):
        app_filepath = os.path.join(FOLDER_APPS,app_file)
        app_filename = ntpath.basename(app_file)
        app_install_id_list = re.findall(r'\d+',app_filename)
        app_install_id = str( app_install_id_list[0] )
        if os.path.isfile(app_filepath):
            current_file = open(app_filepath)
            game_fanart = os.path.join(FOLDER_FANART, 'fanart_' + app_install_id + '.jpg')
            game_icon = os.path.join(FOLDER_ICONS, 'icons_' + app_install_id + '.png')
            game_cover = os.path.join(FOLDER_COVER, 'cover_' + app_install_id + '.jpg')
            
            for line in current_file:
                if line.startswith('Exec='):
                    execute_path = line.replace('Exec=', '')
                if line.startswith('Name='):
                    game_name = line.replace('Name=', '')
                    
                       
            plugintools.add_item( action="launch_game",   title=game_name.encode('utf-8') , thumbnail = game_cover.encode('utf-8') ,    fanart=game_fanart.encode('utf-8') ,    extra = execute_path,   folder=False )

# launch game
def launch_game(params):
    execute_path = params.get('extra')
    game_name = params.get('title')
    try:
            log('attempting to launch: %s' % cmd)
            print cmd.encode('utf-8')
            subprocess.Popen(execute_path, shell=True, close_fds=True)
            kodiBusyDialog()
    except:
            log('ERROR: failed to launch: %s' % cmd)
            print cmd.encode(txt_encode)
            dialog.notification('Aaaaaaargghh!!')
    
    
# Settings dialog
def settings(params):
    plugintools.log("g4gmanager.settings "+repr(params))
    plugintools.open_settings_dialog()

# show a busy dialog
def kodiBusyDialog():
        xbmc.executebuiltin("ActivateWindow(busydialog)")
        log('busy dialog started')
        time.sleep(10)
        xbmc.executebuiltin("Dialog.Close(busydialog)")        

# launch steam
def launchSteam():
	basePath = os.path.join(getAddonInstallPath(), 'resources', 'scripts')
        steamlauncher = os.path.join(basePath, 'steam-launch.sh')
        cmd = steamlauncher
	try:
		log('attempting to launch: %s' % cmd)
		print cmd.encode('utf-8')
                subprocess.Popen(cmd.encode(txt_encode), shell=True, close_fds=True)
                kodiBusyDialog()
	except:
		log('ERROR: failed to launch: %s' % cmd)
		print cmd.encode(txt_encode)
		dialog.notification(language(50123), language(50126), addonIcon, 5000)

# run the program
run()