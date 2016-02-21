# gate4games game manager
# script for managing videogames (download, install, start, remove). Provides different platforms in one interface

import os
import sys
import plugintools
import subprocess
import thread
import threading
import time
import shutil
import stat
import xbmc
import xbmcaddon
import xbmcgui
import xbmcplugin
import collections
import ntpath
import re
import pprint
import xml.etree.ElementTree as ET
import urlparse
import urllib
import urllib2
import json
import zipfile
from qrcodewindow import ShowQrCodeDialog
from downloadwindow import ShowDownloadDialog
from os.path import expanduser

plugintools.module_log_enabled = False
plugintools.http_debug_log_enabled = False

# directories
HOME_DIR = expanduser("~")
THUMBNAIL_PATH = os.path.join(plugintools.get_runtime_path() , "resources" , "img")
ADDON_SCRIPTS_PATH = os.path.join(plugintools.get_runtime_path() , "resources" , "scripts")
FOLDER_QJOYPAD = os.path.join(HOME_DIR, '.qjoypad3')
FOLDER_G4G = os.path.join(HOME_DIR, '.g4g')
FOLDER_SCRIPTS = os.path.join(FOLDER_G4G, 'scripts')
FOLDER_APPS = os.path.join(FOLDER_G4G, 'applications')
FOLDER_IMAGES = os.path.join(FOLDER_G4G, 'images')
FOLDER_ICONS = os.path.join(FOLDER_IMAGES, 'icons')
FOLDER_FANART = os.path.join(FOLDER_IMAGES, 'fanart')
FOLDER_COVER = os.path.join(FOLDER_IMAGES, 'cover')
FOLDER_PROGRESS = os.path.join(FOLDER_G4G, 'progress')
FOLDER_TEMP = os.path.join(FOLDER_G4G, 'temp')
OVERLAY_GAME_RUNNING = os.path.join(FOLDER_TEMP, 'gameinfo')
OVERLAY_RUNS_PATH = os.path.join(FOLDER_TEMP, 'overlay.pid')

# fanarts
FANART = os.path.join(plugintools.get_runtime_path() , "fanart.jpg")
FANART = FANART.encode('utf8')
STEAM_FANART = os.path.join(THUMBNAIL_PATH,"steamfanart.png").encode('utf-8')

# thumbs
DEFAULT_THUMB = os.path.join(THUMBNAIL_PATH,"default.png").encode('utf-8')
LIBRARY_THUMB = os.path.join(THUMBNAIL_PATH,"library.png").encode('utf-8')
LIBRARY_AVAILABLE_THUMB = os.path.join(THUMBNAIL_PATH,"library_available.png").encode('utf-8')
LIBRARY_INSTALLED_THUMB = os.path.join(THUMBNAIL_PATH,"library_installed.png").encode('utf-8')
DOWNLOADS_THUMB = os.path.join(THUMBNAIL_PATH,"downloads.png").encode('utf-8')
FROM_MEDIA_THUMB = os.path.join(THUMBNAIL_PATH,"create_image.png").encode('utf-8')
INTERNET_THUMB = os.path.join(THUMBNAIL_PATH,"internet.png").encode('utf-8')
ADD_GAMES_THUMB = os.path.join(THUMBNAIL_PATH,"add_games.png").encode('utf-8')
SETTINGS_THUMB = os.path.join(THUMBNAIL_PATH,"settings.png").encode('utf-8')
GAMECUBE_THUMB = os.path.join(THUMBNAIL_PATH,"gamecube.jpg").encode('utf-8')
PSX_THUMB = os.path.join(THUMBNAIL_PATH,"psx.png").encode('utf-8')
PS2_THUMB = os.path.join(THUMBNAIL_PATH,"playstation2.png").encode('utf-8')
STEAM_THUMB = os.path.join(THUMBNAIL_PATH,"steamicon.png").encode('utf-8')
ANDROID_THUMB = os.path.join(THUMBNAIL_PATH,"android.png").encode('utf-8')

# urls
API_BASE_URL = "http://www.gate4games.com/index.php?cl=gateosapi"
API_COMPATIBILITY_ATTRIBUTE = "&attributes=CompatibilityTypeLin--Ja"
YOUTUBE_API_KEY='AIzaSyDEJmWgKTSb8Gi4OUmWKY2YrLgI4pIbZQ0'
FREE_ROMS_BASE_URL='http://www.freeroms.com/'
FREE_ROMS_BASE_URL=FREE_ROMS_BASE_URL.encode('utf8')

# internals
addon = xbmcaddon.Addon(id='script.g4g.manager')
addonPath = addon.getAddonInfo('path')
addonIcon = addon.getAddonInfo('icon')
addonVersion = addon.getAddonInfo('version')
dialog = xbmcgui.Dialog()
language = addon.getLocalizedString
scriptid = 'script.g4g.manager'
txt_encode = 'utf-8'


'''
CLASS SECTION

'''

def DirectDownloaderStart(url,dest,title):
    dp = xbmcgui.DialogProgressBG()
    dp.create(language(50209).encode('utf8') + " " + title, language(50210).encode('utf8') + " " + title)
    urllib.urlretrieve(url,dest,lambda nb, bs, fs, url=url: _pbhook(nb,bs,fs,url,dp))
    return dp

def DirectDownloaderUpdate(dp, percent, heading, message):
    dp.update(percent, heading, message)

def DirectDownloaderStop(dp):
    dp.close()
    
def _pbhook(numblocks, blocksize, filesize, url=None,dp=None):
    percent = min((numblocks*blocksize*100)/filesize, 100)
    print percent
    dp.update(percent)

'''
FUNCTION SECTION
'''
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
    log("g4gmanager.run "+repr(params))
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
    
    downloads_count = len([f for f in os.listdir(FOLDER_PROGRESS) if os.path.isfile(os.path.join(FOLDER_PROGRESS, f))])
    download_title = language(50005).encode('utf-8') + ' (' + str(downloads_count) + ')'
    if downloads_count > 0:
        download_title = '[COLOR green]' + download_title + '[/COLOR]'
    
    plugintools.add_item( action="steam_selection", title=language(50001).encode('utf-8'), thumbnail=STEAM_THUMB, fanart=STEAM_FANART , folder=True )
    plugintools.add_item( action="library_selection", title=language(50002).encode('utf-8') , thumbnail=LIBRARY_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="add_games", title=language(50003).encode('utf-8') , thumbnail=ADD_GAMES_THUMB , fanart=FANART , folder=True )
    plugintools.add_item( action="downloads_overview", title=download_title , thumbnail=DOWNLOADS_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="settings", title=language(50004).encode('utf-8') , thumbnail=SETTINGS_THUMB , fanart=FANART , folder=False )


# dummy message
def dummy(params):
    plugintools.message("Gate4Games Manager", language(59999).encode('utf8'),"")


# steam selection
def steam_selection(params):
    log("g4gmanager.selection "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)
    plugintools.add_item( action="launch_steam", title=language(50040).encode('utf-8'), thumbnail=STEAM_THUMB, fanart=STEAM_FANART , folder=False )
    

# launch steam session
def launch_steam():
    steamlauncher = os.path.join(ADDON_SCRIPTS_PATH, 'steam-launch.sh')
    cmd = '"%s"' % (steamlauncher)
    try:
        log('attempting to launch: %s' % cmd)
        print cmd.encode('utf-8')
        subprocess.Popen(cmd.encode(txt_encode), shell=True, close_fds=True)
    except:
        log('ERROR: failed to launch: %s' % cmd)
        print cmd.encode(txt_encode)
        

# add games
def add_games(params):
    log("g4gmanager.add_games "+repr(params))
    
    plugintools.set_view(plugintools.THUMBNAIL)
    
    default_filter_and_sort = json.dumps(dict([('genre', ''), ('sortby', 'relevance'), ('sortdir', 'desc')]))
    
    plugintools.add_item( action="add_pc_games", title=language(50020).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART, extra=default_filter_and_sort, folder=True, page="1" )
    plugintools.add_item( action="dummy", title=language(50021).encode('utf-8'), thumbnail=ANDROID_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="add_choose_rom_source", title=language(50022).encode('utf-8'), extra="psx", thumbnail=PSX_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="dummy", title=language(50023).encode('utf-8'), thumbnail=PS2_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="add_choose_rom_source", title=language(50024).encode('utf-8'), extra="nintendo_gamecube", thumbnail=GAMECUBE_THUMB, fanart=FANART , folder=True )


# choose source for gettings
def add_choose_rom_source(params):
    log("g4gmanager.add_choose_rom_source "+repr(params))
    
    plugintools.set_view(plugintools.THUMBNAIL)
    
    thumbnail = params.get('thumbnail')
    platform = params.get('extra')
    
    plugintools.add_item( action="dummy", title=language(50025).encode('utf-8'), extra=platform, thumbnail=FROM_MEDIA_THUMB, fanart=thumbnail , folder=False )
    plugintools.add_item( action="add_freeroms_games_letters", title=language(50026).encode('utf-8'), extra=platform, thumbnail=INTERNET_THUMB, fanart=thumbnail , folder=True )
    

# choose game by letter    
def add_freeroms_games_letters(params):
    log("g4gmanager.add_psx_games_letters "+repr(params))
    platform = params.get('extra')
    # action = platform + "_letter_list"
    action = "freeroms_letter_list"
    
    plugintools.set_view(plugintools.LIST)
    
    free_roms_num       = FREE_ROMS_BASE_URL + platform + "_roms_NUM.htm"
    free_roms_a         = FREE_ROMS_BASE_URL + platform + "_roms_A.htm"
    free_roms_b         = FREE_ROMS_BASE_URL + platform + "_roms_B.htm"
    free_roms_c         = FREE_ROMS_BASE_URL + platform + "_roms_C.htm"
    free_roms_d         = FREE_ROMS_BASE_URL + platform + "_roms_D.htm"
    free_roms_e         = FREE_ROMS_BASE_URL + platform + "_roms_E.htm"
    free_roms_f         = FREE_ROMS_BASE_URL + platform + "_roms_F.htm"
    free_roms_g         = FREE_ROMS_BASE_URL + platform + "_roms_G.htm"
    free_roms_h         = FREE_ROMS_BASE_URL + platform + "_roms_H.htm"
    free_roms_i         = FREE_ROMS_BASE_URL + platform + "_roms_I.htm"
    free_roms_j         = FREE_ROMS_BASE_URL + platform + "_roms_J.htm"
    free_roms_k         = FREE_ROMS_BASE_URL + platform + "_roms_K.htm"
    free_roms_l         = FREE_ROMS_BASE_URL + platform + "_roms_L.htm"
    free_roms_m         = FREE_ROMS_BASE_URL + platform + "_roms_M.htm"
    free_roms_n         = FREE_ROMS_BASE_URL + platform + "_roms_N.htm"
    free_roms_o         = FREE_ROMS_BASE_URL + platform + "_roms_O.htm"
    free_roms_p         = FREE_ROMS_BASE_URL + platform + "_roms_P.htm"
    free_roms_q         = FREE_ROMS_BASE_URL + platform + "_roms_Q.htm"
    free_roms_r         = FREE_ROMS_BASE_URL + platform + "_roms_R.htm"
    free_roms_s         = FREE_ROMS_BASE_URL + platform + "_roms_S.htm"
    free_roms_t         = FREE_ROMS_BASE_URL + platform + "_roms_T.htm"
    free_roms_u         = FREE_ROMS_BASE_URL + platform + "_roms_U.htm"
    free_roms_v         = FREE_ROMS_BASE_URL + platform + "_roms_V.htm"
    free_roms_w         = FREE_ROMS_BASE_URL + platform + "_roms_W.htm"
    free_roms_x         = FREE_ROMS_BASE_URL + platform + "_roms_X.htm"
    free_roms_y         = FREE_ROMS_BASE_URL + platform + "_roms_X.htm"
    free_roms_z         = FREE_ROMS_BASE_URL + platform + "_roms_Z.htm"
    
    plugintools.add_item( action=action, url=free_roms_num,   title="#", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_a,     title="A", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_b,     title="B", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_c,     title="C", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_d,     title="D", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_e,     title="E", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_f,     title="F", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_g,     title="G", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_h,     title="H", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_i,     title="I", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_j,     title="J", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_k,     title="K", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_l,     title="L", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_m,     title="M", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_n,     title="N", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_o,     title="O", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_p,     title="P", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_q,     title="Q", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_r,     title="R", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_s,     title="S", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_t,     title="T", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_u,     title="U", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_v,     title="V", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_w,     title="W", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_x,     title="X", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_y,     title="Y", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action=action, url=free_roms_z,     title="Z", extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )


# list games for letter    
def freeroms_letter_list(params):
    log("g4gmanager.freeroms_letter_list "+repr(params))

    platform = params.get('extra')
    url = params.get('url')
    
    body,response_headers = plugintools.read_body_and_headers(url)
    
    # only roms that provide images
    #pattern = '<a href="([^"]+)">([^>]+)<img[^<]+'
    pattern = '<td align=left nowrap><a href="([^"]+)">([^>]+)<[^<]+'
    games = plugintools.find_multiple_matches(body,pattern)    
    
    for game in games:
        details_url = game[0]
        details_title = game[1]
        plugintools.add_item( action="freeroms_game_details", url=details_url,   title=details_title, extra=platform, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )


# detail actions for freeroms game
def freeroms_game_details(params):
    log("g4gmanager.freeroms_game_details"+repr(params))
    
    url = params.get('url')
    freeroms_title = params.get('title')
    platform = params.get('extra')
    
    body,response_headers = plugintools.read_body_and_headers(url)
    
    pattern = '&nbsp;<a href="([^"]+)">[^>]+'
    zip_source = plugintools.find_single_match(body,pattern)
    
    pattern = '<img src="([^"]+)" alt="[^"]+" width=300><BR>+'
    game_images = plugintools.find_multiple_matches(body,pattern)
    
    screenshot = ''
    game_image_count = 1
    cover_image = get_default_thumb_by_platform(platform)
    for game_image in game_images:
        if game_image_count == 1:
            cover_image = game_image
        else:
            screenshot = game_image
        game_image_count += 1
    if screenshot == '':
        screenshot = cover_image

    plugintools.add_item( action="install_freeroms_game", url=zip_source, title=language(50209).encode('utf8') + " " + freeroms_title, thumbnail=cover_image, fanart=screenshot, actorsandmore=screenshot, plot=freeroms_title, extra=platform, folder=False )

# returns default thumb by platform
def get_default_thumb_by_platform(platform):
    switcher = {
        'psx'                   : PSX_THUMB,
        'nintendo_gamecube'     : GAMECUBE_THUMB,
    }
    
    return switcher.get(platform, "")
    

    
# downloads and installs freeroms game    
def install_freeroms_game(params):
    log("g4gmanager.install_freeroms_game "+repr(params))
    
    install_title = params.get('title')
    title = params.get('plot')
    platform = params.get('extra')


    install_message = language(50216).encode('utf8') + " " + title + " " + language(50217).encode('utf8')
    if dialog.yesno(install_title, install_message):
        image = params.get('thumbnail')
        fanart = params.get('actorsandmore')
        url = params.get('url')
        systemtype = platform
        downloadtype = "direct"
        packagetype = "zip"
        basePath = os.path.join(getAddonInstallPath(), 'resources', 'scripts')
        install_script = os.path.join(basePath, 'install.py')        
        cmd = "python " + install_script + ' --url="' + url + '" --downloadtype="' + downloadtype + '" --image="' + image + '" --name="' + title + '" --systemtype="' + systemtype + '" --packagetype="' + packagetype + '" --fanart="' + fanart + '"'
        log("g4gmanager.install_psx_game => trigger command: "+ cmd)
        subprocess.Popen(cmd, shell=True, close_fds=True)
        notification_title = language(50224).encode('utf8') + " " + language(50201).encode('utf8') + " " + title + " " + language(50225).encode('utf8')
        notification_message = language(50226).encode('utf8')
        xbmc.executebuiltin('Notification(' + notification_title + ',' + notification_message + ',5000,' + image + ')')

# checks application dir iterate through all files and take the highest value add 1 and transfer it to 8 letter length string 
def get_next_game_id():
    highest_id = 0
    
    for filename in os.listdir(HOME_DIR + '/.g4g/scripts/'):
        splitted_filename = filename.split("_")
        current_id = splitted_filename[1]
        id_value = int(current_id)
        if id_value > highest_id:
            highest_id = id_value
    for filename in os.listdir(HOME_DIR + '/.g4g/downloads/'):
        highest_id += 1
        
    highest_id += 1
    next_id = str(highest_id)
    next_id = next_id.zfill(8)
    
    return next_id

    
# add games
def add_pc_games(params):
    log("g4gmanager.add_pc_games "+repr(params))
    
    page = params.get('page') 
    extra = json.loads(params.get('extra'))
    
    # category filter?
    add_genre = ""
    if extra != '' and extra['genre'] != None and extra['genre'] != '':
        add_genre = "|GameGenre--" + urllib.quote_plus(extra['genre'])
    
    # sorting ?
    add_sortby = ""
    if extra != '' and extra['sortby'] != None and extra['sortby'] != '':
        add_sortby = "&sortby=" + urllib.quote_plus(extra['sortby'])
            
    add_sortdir = ""
    if extra != '' and extra['sortdir'] != None and extra['sortdir'] != '':
        add_sortdir = "&sortdir=" + urllib.quote_plus(extra['sortdir'])
        
    attributes_filter = API_COMPATIBILITY_ATTRIBUTE + add_genre
    
    url = API_BASE_URL + attributes_filter + add_sortby + add_sortdir
    
    if page > 1:
        url += "&page=" + str(page)
    log("g4gmanager.add_pc_games.url is "+ url)
    
    body,response_headers = plugintools.read_body_and_headers(url)
    
    root = ET.fromstring(body)
    log("g4gmanager.root "+root.tag)
    
    # get list information
    extra['maxpage'] = "1"
    for listinfos in root.iter('listinfos'):
        maxpage = listinfos.find('maxpage').text
        extra['maxpage'] = str(maxpage)
    
    plugintools.set_view(plugintools.LIST)
    
    maintenance_extra = json.dumps(extra)
    # add maintenance entries
    plugintools.add_item( action="main_list", title=language(50064).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=maintenance_extra, folder=True )
    plugintools.add_item( action="add_pc_game_to_page", title=language(50050).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=maintenance_extra, folder=True, page=str(page) )
    plugintools.add_item( action="add_pc_game_by_genre", title=language(50051).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=maintenance_extra, folder=True )
    plugintools.add_item( action="add_pc_game_sort", title=language(50052).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=maintenance_extra, folder=True )

    for product in root.iter('product'):
        log("g4gmanager.product "+repr(product))
        product_id = product.find('id').text
        name = product.find('name').text
        currency = product.find('currency').text
        fromprice = product.find('fromprice').text
        fromprice = float(fromprice)
        fromprice = "{:6.2f}".format(fromprice)
        fromprice = str(fromprice)
        fromprice = fromprice.replace('.', ',')
        coverpic = product.find('coverpic').text
        extra['id'] = product_id
        details_extra = json.dumps(extra)
        
        title = name.encode('utf-8') + " " + language(50200).encode('utf-8') + "[COLOR red]" + fromprice + " " + currency  + "[/COLOR]"
        
        plugintools.add_item( action="add_pc_game", title=title, thumbnail=coverpic, fanart=coverpic , extra=details_extra, folder=True )
        
    # add prev and next page entry if there are still pages
    log("g4gmanager.current_page "+str(page)+ " maxpage " + str(maxpage))
    if int(page) < int(maxpage):
        next_page = int(page) + 1
        next_page_title = "[COLOR blue]" + language(50053).encode('utf-8') + " " +  str(next_page) + " " + language(50201).encode('utf-8') + " " + str(maxpage) + "[/COLOR]"
        plugintools.add_item( action="add_pc_games", title=next_page_title, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=maintenance_extra, folder=True, page=str(next_page)  )


# directly jumping to certain page        
def add_pc_game_to_page(params):
    log("g4gmanager.add_pc_game_to_page "+repr(params))
    page = params.get('page') 
    extra = json.loads(params.get('extra'))
    
    plugintools.set_view(plugintools.LIST)
    
    maxpage = extra['maxpage']
    extra = json.dumps(extra)
    
    for current_page in range(1, int(maxpage)):
        title_page = str(current_page)
        if current_page == int(page):
            title_page = "[COLOR green]" +  title_page + "[/COLOR]"
            
        plugintools.add_item( action="add_pc_games", title=title_page, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=extra, folder=True, page=str(current_page)  )


# get genre filtered list
def add_pc_game_by_genre(params):
    extra = json.loads(params.get('extra'))
    
    current_genre = ""
    if extra != '' and extra['genre'] != None and extra['genre'] != '':
        current_genre = extra['genre']
    
    attributes_filter = API_COMPATIBILITY_ATTRIBUTE
    
    url = API_BASE_URL + attributes_filter + "&fnc=lvGetGenres"
    log("g4gmanager.add_pc_game_by_genre.url is "+ url)
    
    body,response_headers = plugintools.read_body_and_headers(url)
    
    root = ET.fromstring(body)
    log("g4gmanager.add_pc_game_by_genre.root "+root.tag)
    
    plugintools.set_view(plugintools.LIST)
    
    #reset entry
    extra['genre'] = ""
    genre_values = json.dumps(extra)
    plugintools.add_item( action="add_pc_games", title=language(50065).encode('utf8'), thumbnail=DEFAULT_THUMB, fanart=FANART , extra=genre_values, folder=True, page="1"  )
    
    for genre in root.iter('genre'):
        genre_name = genre.find('name').text
        genre_name = genre_name.encode('utf8')
        if current_genre == genre_name:
            genre_name = "[COLOR green]" + genre_name + "[/COLOR]"
        extra['genre'] = genre_name
        genre_values = json.dumps(extra)
        plugintools.add_item( action="add_pc_games", title=genre_name, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=genre_values, folder=True, page="1"  )


# get sorted list
def add_pc_game_sort(params):
    extra = json.loads(params.get('extra'))
    plugintools.set_view(plugintools.LIST)
    
    # maybe there are former filters for category so we should use it
    if extra != '' and extra['genre'] != None and extra['genre'] != '':
        filter_genre = extra['genre']
    else:
        filter_genre = ""
    
    # prepare values for sortings
    sortby_relevance_desc = json.dumps(dict([('genre', filter_genre), ('sortby', 'relevance'), ('sortdir', 'desc')]))
    sortby_relevance_asc = json.dumps(dict([('genre', filter_genre), ('sortby', 'relevance'), ('sortdir', 'asc')]))
    sortby_name_asc = json.dumps(dict([('genre', filter_genre), ('sortby', 'name'), ('sortdir', 'asc')]))
    sortby_name_desc = json.dumps(dict([('genre', filter_genre), ('sortby', 'name'), ('sortdir', 'desc')]))
    sortby_price_asc = json.dumps(dict([('genre', filter_genre), ('sortby', 'price'), ('sortdir', 'asc')]))
    sortby_price_desc = json.dumps(dict([('genre', filter_genre), ('sortby', 'price'), ('sortdir', 'desc')]))
    sortby_igdb_desc = json.dumps(dict([('genre', filter_genre), ('sortby', 'igdb'), ('sortdir', 'desc')]))
    sortby_igdb_asc = json.dumps(dict([('genre', filter_genre), ('sortby', 'igdb'), ('sortdir', 'asc')]))
    sortby_release_desc = json.dumps(dict([('genre', filter_genre), ('sortby', 'release'), ('sortdir', 'desc')]))
    sortby_release_asc = json.dumps(dict([('genre', filter_genre), ('sortby', 'release'), ('sortdir', 'asc')]))
    
    # titles
    title_sortby_relevance_desc = language(50054).encode('utf8')
    title_sortby_relevance_asc = language(50055).encode('utf8')
    title_sortby_name_asc = language(50056).encode('utf8')
    title_sortby_name_desc = language(50057).encode('utf8')
    title_sortby_price_asc = language(50058).encode('utf8')
    title_sortby_price_desc = language(50059).encode('utf8')
    title_sortby_igdb_desc = language(50060).encode('utf8')
    title_sortby_igdb_asc = language(50061).encode('utf8')
    title_sortby_release_desc = language(50062).encode('utf8')
    title_sortby_release_asc = language(50063).encode('utf8')
    
    plugintools.add_item( action="add_pc_games", title=title_sortby_relevance_desc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_relevance_desc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_relevance_asc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_relevance_asc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_name_asc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_name_asc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_name_desc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_name_desc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_price_asc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_price_asc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_price_desc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_price_desc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_igdb_desc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_igdb_desc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_igdb_asc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_igdb_asc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_release_desc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_release_desc, folder=True, page="1" )
    plugintools.add_item( action="add_pc_games", title=title_sortby_release_asc, thumbnail=DEFAULT_THUMB, fanart=FANART , extra=sortby_release_asc, folder=True, page="1" )


# game details page        
def add_pc_game(params):
    extra = json.loads(params.get('extra'))
    
    product_id = extra['id']
    url = API_BASE_URL + "&id=" + product_id
    log("g4gmanager.add_pc_game.url is "+ url)
    
    body,response_headers = plugintools.read_body_and_headers(url)
    plugintools.set_view(plugintools.LIST)
    
    root = ET.fromstring(body)
    log("g4gmanager.add_pc_game.root "+root.tag)
    
    #get general informations
    product_name = root.find('name').text
    product_coverpic = root.find('coverpic').text
    product_fanart = root.find('fanart').text
    if product_fanart is None:
        product_fanart = params.get('fanart')
        
    if product_fanart is None:
        product_fanart = ""

    log("g4gmanager.add_pc_game.product_name "+product_name)
    log("g4gmanager.add_pc_game.product_coverpic "+product_coverpic)
    log("g4gmanager.add_pc_game.product_fanart "+product_fanart)
    
    # checking amount for trailers and reviews 
    trailers_count = 0
    for trailer in root.iter('trailer'):
        trailer_video_url = trailer.find('videourl').text
        trailers_count += 1
            
    reviews_count = 0
    for reviews in root.iter('review_video'):
        review_video_url = reviews.find('videourl').text
        reviews_count += 1
    
    details_standard_extra = json.dumps(extra)
    # details standard
    details_for_product = language(50202).encode('utf8') + " " + product_name
    trailer_for_product = language(50203).encode('utf8') + " " + product_name
    reviews_for_product = language(50204).encode('utf8') + " " + product_name
    pictures_for_product = language(50207).encode('utf8') + " " + product_name
    
    plugintools.add_item( action="dummy", title=details_for_product, thumbnail=product_coverpic, fanart=product_fanart, extra=details_standard_extra, folder=False )
    if trailers_count > 1:
        plugintools.add_item( action="add_pc_game_trailer", title=trailer_for_product, thumbnail=product_coverpic, fanart=product_fanart, extra=details_standard_extra, folder=True )
    elif trailers_count == 1:
        plugintools.add_item( action="play_youtube_video", url=trailer_video_url, title=trailer_for_product, thumbnail=product_coverpic, fanart=product_fanart, extra=details_standard_extra, folder=False )
    if reviews_count > 1:
        plugintools.add_item( action="add_pc_game_review", title=reviews_for_product, thumbnail=product_coverpic, fanart=product_fanart, extra=details_standard_extra, folder=True )
    elif reviews_count == 1:
        plugintools.add_item( action="play_youtube_video", url=review_video_url, title=reviews_for_product, thumbnail=product_coverpic, fanart=product_fanart, extra=details_standard_extra, folder=False )
    plugintools.add_item( action="dummy", title=pictures_for_product, thumbnail=product_coverpic, fanart=product_fanart, extra=details_standard_extra, folder=False )
    
    for vendor in root.iter('vendor'):
        vendorname = vendor.find('vendorname').text
        vendoricon = vendor.find('vendoricon').text
        vendorlink = vendor.find('vendorlink').text
        vendorqrcode = vendor.find('vendorqrcode').text
        vendorprice = vendor.find('vendorprice').text
        vendorprice = float(vendorprice)
        vendorprice = "{:10.2f}".format(vendorprice)
        vendorprice = str(vendorprice)
        vendorprice = vendorprice.replace('.', ',')
        
        vendortitle = "[COLOR red]" + vendorprice + " EUR [/COLOR]" + product_name + " " + language(50208).encode('utf8') + " " + vendorname
        
        plugintools.add_item( action="show_qr_code", title=vendortitle, thumbnail=vendoricon, fanart=product_fanart, plot=vendorqrcode, extra=details_standard_extra, folder=False )


# shows qr code of product
def show_qr_code(params):
    vendorqrcode = params.get('plot')
    qrcodeisplay=ShowQrCodeDialog()
    qrcodeisplay.setGameQrCode(vendorqrcode)
    qrcodeisplay.doModal()


# play trailer for game    
def add_pc_game_trailer(params):
    extra = json.loads(params.get('extra'))
    
    product_id = extra['id']
    url = API_BASE_URL + "&id=" + product_id
    log("g4gmanager.add_pc_game_trailer.url is "+ url)
    
    body,response_headers = plugintools.read_body_and_headers(url)
    plugintools.set_view(plugintools.LIST)
    
    root = ET.fromstring(body)
    log("g4gmanager.add_pc_game_trailer.root "+root.tag)

    #get general informations
    product_name = root.find('name').text
    product_coverpic = root.find('coverpic').text
    product_fanart = root.find('fanart').text
    if product_fanart is None:
        product_fanart = params.get('fanart')
        
    if product_fanart is None:
        product_fanart = ""
        
    trailer_for_product = language(50205).encode('utf8') + " " + product_name
    for trailer in root.iter('trailer'):
        video_url = trailer.find('videourl').text
        plugintools.add_item( action="play_youtube_video", title=trailer_for_product, url=video_url, thumbnail=product_coverpic, fanart=product_coverpic, folder=False )


# play review for pc game    
def add_pc_game_review(params):
    extra = json.loads(params.get('extra'))
    
    product_id = extra['id']
    url = API_BASE_URL + "&id=" + product_id
    log("g4gmanager.add_pc_game_review.url is "+ url)
    
    body,response_headers = plugintools.read_body_and_headers(url)
    plugintools.set_view(plugintools.LIST)
    
    root = ET.fromstring(body)
    log("g4gmanager.add_pc_game_review.root "+root.tag)

    #get general informations
    product_name = root.find('name').text
    product_coverpic = root.find('coverpic').text
    product_fanart = root.find('fanart').text
    if product_fanart is None:
        product_fanart = params.get('fanart')
        
    if product_fanart is None:
        product_fanart = ""
        
    review_for_product = language(50206).encode('utf8') + " " + product_name
    for review_video in root.iter('review_video'):
        video_url = review_video.find('videourl').text
        plugintools.add_item( action="play_youtube_video", title=review_for_product, url=video_url, thumbnail=product_coverpic, fanart=product_coverpic, folder=False )

    
# plays a youtube video using the internal plugin    
def play_youtube_video(params):
    xbmc.executebuiltin("ActivateWindow(busydialog)")
    from urlresolver.types import HostedMediaFile
    video_url = params.get('url')
    hosted_media_file = HostedMediaFile(url=video_url)
    media_url = hosted_media_file.resolve()
    xbmc.executebuiltin("xbmc.PlayMedia("+media_url+")")
    xbmc.executebuiltin("Dialog.Close(busydialog)")        


# library selections    
def library_selection(params):
    log("g4gmanager.library_selection "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)
    
    plugintools.add_item( action="library_installed", title=language(50010).encode('utf-8') , thumbnail=LIBRARY_INSTALLED_THUMB , fanart=FANART , folder=True )
    plugintools.add_item( action="library_available", title=language(50011).encode('utf-8') , thumbnail=LIBRARY_AVAILABLE_THUMB , fanart=FANART , folder=True )


#show available games which are not installed from steam,gog,amazon
def library_available(params):
    log("g4gmanager.library_available "+repr(params))
    
    #currently this is only a dummy entry for testing the custom download progress dialog
    extra = dict([('downloadtype', 'direct'), ('packagetype', 'zip'), ('appid', '12345'), ('url', 'http://www.irgendwo.de/mypack.zip')])
    extra = json.dumps(extra)
    
    plugintools.add_item( action="details_available", title="Sample Game" , thumbnail=DEFAULT_THUMB , fanart=FANART, extra=extra , folder=True )


# available actions for certain available game
def details_available(params):
    log("g4gmanager.details_available "+repr(params))
    
    extra = params.get('extra')
    plugintools.add_item( action="start_install_game", title="Install Sample Game" , thumbnail=DEFAULT_THUMB , fanart=FANART, extra=extra , folder=False )


# trigger the install script which will do the job and report about it in json files
def start_install_game(params):
    log("g4gmanager.start_install_game "+repr(params))
    
    options = json.loads(params.get('extra'))
    name = params.get('title')
    thumbnail = params.get('thumbnail')
    basePath = os.path.join(getAddonInstallPath(), 'resources', 'scripts')
    install_script = os.path.join(basePath, 'install.py')
    cmd = "%s" % (install_script)
    log("g4gmanager.start_install_game => CMD: " + cmd)
    #args = [install_script.encode('utf8'), "-dt", options['downloadtype'], "-pt", options['packagetype'], "-aid", options['appid'], "-u", options['url'], "-n", name]
    #subprocess.Popen(args, shell=True, close_fds=True)
    subprocess.Popen(install_script, shell=True, close_fds=True)
    notification_title = language(50224).encode('utf8') + " " + language(50201).encode('utf8') + " " + name + " " + language(50225).encode('utf8')
    notification_message = language(50226).encode('utf8')
    xbmc.executebuiltin('Notification(' + notification_title + ',' + notification_message + ',5000,' + thumbnail + ')')
    

# overview page of running downloads    
def downloads_overview(params):
    log("g4gmanager.downloads_overview "+repr(params))
    
    thumbnail = params.get('thumbnail')
    
    plugintools.set_view(plugintools.LIST)
    
    # initial 
    for progress_file in os.listdir(FOLDER_PROGRESS):
        progress_filepath = os.path.join(FOLDER_PROGRESS,progress_file)
        handle = int(sys.argv[1])
        if os.path.isfile(progress_filepath):
            current_file = open(progress_filepath,'r')
            with current_file as json_file:
                progress_info = json.load(json_file)
                title = progress_info['name'] # + " (" + str(progress_info['percent']) + " %)"
                listitem = plugintools.add_item( action="show_download_progress", title=title , extra=progress_filepath, thumbnail=DEFAULT_THUMB , fanart=FANART , folder=False )
                #update_listitem_thread = threading.Thread(target=update_progress_listitem, args=(handle, listitem, progress_filepath))
                #update_listitem_thread.daemon = True
                #update_listitem_thread.start()    
            current_file.close()
                
    # periodically update list


# Shows download progress
def show_download_progress(params):
    progress_filepath = params.get('extra')
    current_file = open(progress_filepath,'r')
    with current_file as json_file:
        try:
            progress_info = json.load(json_file)
            downloaddisplay=ShowDownloadDialog()
            downloaddisplay.setDownloadPercent(progress_info['percent'])
            downloaddisplay.setDownloadTitle(progress_info['name'])
            downloaddisplay.setDownloadImage(progress_info['image'])
            downloaddisplay.setDownloadMessage(progress_info['message'])
            downloaddisplay.setDownloadRemainingTime(progress_info['remainingtime'])
            downloaddisplay.setDownloadCurrentRate(progress_info['currentrate'])
            update_thread = threading.Thread(target=update_progress_details, args=(downloaddisplay, progress_filepath))
            update_thread.daemon = True
            update_thread.start()    
            downloaddisplay.doModal()
        except:
            pass
    current_file.close()
    #update_thread.set()


# Thread to periodically update progress of a certain task
def update_progress_details(window, filepath):
    while os.path.isfile(filepath):
        current_file = open(filepath,'r')
        try:
            with current_file as json_file:
                progress_info = json.load(json_file)
                log("g4gmanager.update_progress_details "+repr(progress_info))
                window.updateDownloadPercent(progress_info['percent'])  
                window.updateDownloadMessage(progress_info['message'])
                window.updateDownloadRemainingTime(progress_info['remainingtime'])
                window.updateDownloadCurrentRate(progress_info['currentrate'])
                window.setProgressFilePath(filepath)
        except:
            pass
        time.sleep(1)
        current_file.close()
    

# Thread to periodically update listlabel
def update_progress_listitem(handle, listitem, filepath):
    while os.path.isfile(filepath):
        current_file = open(filepath,'r')
        with current_file as json_file:
            progress_info = json.load(json_file)
            title = progress_info['name'] + " (" + str(progress_info['percent']) + ")"
            log("g4gmanager.update_progress_listitem "+repr(progress_info))
            log("g4gmanager.update_progress_listitem => Current Label: "+ listitem.getLabel())
            listitem.setLabel(title)
        current_file.close()
        log("g4gmanager.update_progress_listitem => Update Container")
        # xbmc.executebuiltin("Container.Refresh")
        time.sleep(1)

    
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
                    execute_path = execute_path.strip()
                if line.startswith('Name='):
                    game_name = line.replace('Name=', '')
                    game_name = game_name.strip()
                if line.startswith('Type='):
                    game_type = line.replace('Type=', '')
                    game_type = game_type.strip()
                if line.startswith('Type='):
                    pc_game_type = line.replace('PCType=', '')
                    pc_game_type = pc_game_type.strip()

            game_infos = {
                'execute_path': '',
            }
                        
                    
            plugintools.add_item( action="installed_app_actions", title=game_name.encode('utf-8') , thumbnail=game_cover.encode('utf-8') , fanart=game_fanart.encode('utf-8') , extra=app_install_id, folder=True )
            

# actions that can be done with selected app            
def installed_app_actions(params):
    log("g4gmanager.library_selection "+repr(params))
    game_name = params.get('title')
    game_cover = params.get('thumbnail')
    game_fanart = params.get('fanart')
    app_install_id = params.get('extra')
    app_filepath = os.path.join(FOLDER_APPS, 'game_' + app_install_id + '.desktop')
    current_file = open(app_filepath)
    
    for line in current_file:
        if line.startswith('Exec='):
            execute_path = line.replace('Exec=', '')
            execute_path = execute_path.strip()
        if line.startswith('Name='):
            game_name = line.replace('Name=', '')
            game_name = game_name.strip()

    current_file.close()
    plugintools.set_view(plugintools.LIST)
    
    start_app_caption = language(50212).encode('utf8') + " " + game_name.encode('utf-8')
    remove_app_caption = language(50213).encode('utf8') + " " + game_name.encode('utf-8')
    
    #actions
    plugintools.add_item( action="launch_app", title=start_app_caption , thumbnail=game_cover.encode('utf-8') , fanart=game_cover.encode('utf-8') , extra=execute_path, actorsandmore=app_install_id, folder=False )
    plugintools.add_item( action="remove_app", title=remove_app_caption , thumbnail=game_cover.encode('utf-8') , fanart=game_cover.encode('utf-8') , actorsandmore=game_name.encode('utf-8'), extra=app_install_id, folder=False )
    
# remove app
def remove_app(params):
    dialog = xbmcgui.Dialog()
    remove_title = params.get('title')
    game_title = params.get('actorsandmore')
    remove_message = language(50216).encode('utf8') + " " + game_title + " " + language(50215).encode('utf8')
    if dialog.yesno(remove_title, remove_message):
        log("g4gmanager.remove_app => answer yes")
        dp = xbmcgui.DialogProgressBG()
        dp.create(remove_title, language(50221).encode('utf8'))
        xbmc.sleep(1000)

        app_install_id          = params.get('extra')
        script_filepath         = HOME_DIR + "/.g4g/scripts/script_" + app_install_id
        desktop_filepath        = HOME_DIR + "/.g4g/applications/game_" + app_install_id + ".desktop"
        #icon_filepath          = HOME_DIR + "/.g4g/images/icons/icon_" + next_id + ".jpg"
        cover_filepath          = HOME_DIR + "/.g4g/images/cover/cover_" + app_install_id + ".jpg"
        fanart_filepath         = HOME_DIR + "/.g4g/images/fanart/fanart_" + app_install_id + ".jpg"
        rom_base_path           = HOME_DIR + "/.g4g/roms/"
        
        app_filepath = os.path.join(FOLDER_APPS, 'game_' + app_install_id + '.desktop')
        current_file = open(app_filepath)
        
        for line in current_file:
            if line.startswith('Type='):
                game_type = line.replace('Type=', '')
            if line.startswith('Type='):
                pc_game_type = line.replace('PCType=', '')
        
        game_type = game_type.strip()

        dp.update(25,remove_title,language(50213).encode('utf8') + " " + language(50219).encode('utf8'))
        xbmc.sleep(1000)
        delete_path = None
        delete_file = None
        log("g4gmanager.remove_app => game_type: '" + game_type  + "'")        
        if game_type == 'psx':
            rom_psx_path = rom_base_path + 'psx/'
            log("g4gmanager.remove_app => rom_psx_path: " + rom_psx_path)        
            rom_filename = "rom_" + app_install_id + ".bin"
            delete_file = os.path.join(rom_psx_path,rom_filename)
        if game_type == 'nintendo_gamecube':
            rom_gamecube_path = rom_base_path + 'gamecube/'
            log("g4gmanager.remove_app => rom_gamecube_path: " + rom_gamecube_path)        
            rom_filename = "rom_" + app_install_id + ".iso"
            delete_file = os.path.join(rom_gamecube_path,rom_filename)

        log("g4gmanager.remove_app => delete_file: " + delete_file)        
        if delete_file is not None:
            os.remove(delete_file)
            dp.update(50, remove_title,language(50213).encode('utf8') + " " + language(50220).encode('utf8'))
            xbmc.sleep(1000)
            os.remove(script_filepath)
            os.remove(desktop_filepath)
            os.remove(fanart_filepath)
            os.remove(cover_filepath)
            
        if delete_path is not None:            
            log("g4gmanager.remove_app => delete_path: " + delete_path)        
            # delete complete path with all subfiles via shutil.rmtree() => will delete a directory and all its contents. 
            
        dp.update(75, remove_title,language(50222).encode('utf8'))
        # remove textures db to force regenarating thumbnails
        textures_db_path = HOME_DIR + "/.kodi/userdata/Database/Textures13.db"
        if os.path.isfile(textures_db_path):
            os.remove(textures_db_path)
            
        dp.update(100, remove_title, game_title + " " + language(50214).encode('utf8'))
        xbmc.sleep(1000)
        dp.close()
        
            
# launch app
def launch_app(params):
    execute_path = params.get('extra')
    game_name = str( params.get('title') )
    app_install_id = params.get('actorsandmore')
    
    gamepad_layout_file = 'layout_' + app_install_id
    gamepad_layout_filepath = os.path.join(FOLDER_QJOYPAD, gamepad_layout_file)
    
    try:
            log('attempting to launch: %s' % execute_path)
            log('GAME RUNNING PATH:' + OVERLAY_GAME_RUNNING)
            # make sure overlay is clear and reset
            if os.path.isfile(OVERLAY_GAME_RUNNING):
                log("REMOVE OVERLAY_GAME_RUNNING")
                os.remove(OVERLAY_GAME_RUNNING)
            if os.path.isfile(OVERLAY_RUNS_PATH):
                lof("OPEN OVERLAY_RUNS_PATH")
                pidfile = open(OVERLAY_RUNS_PATH, 'r')
                with pidfile as f:
                    pid_to_kill = f.readline()
                if pid_to_kill != None:
                    # kill process
                    os.kill(int(pid_to_kill.strip()), signal.SIGTERM)
                    pidfile.close()
                    os.remove(OVERLAY_RUNS_PATH)
            
            # placing game infos in temp for overlay
            game_name_parts = game_name.split(' ')
            log("Game name parts: " + repr(game_name_parts))
            extracted_game_name = ""
            parts_index = 0
            for game_name_part in game_name_parts:
                if parts_index > 0:
                    extracted_game_name += game_name_part + " "
                parts_index += 1
                
            extracted_game_name = extracted_game_name.strip()
            
            log("Name extracted: " + repr(extracted_game_name))
            gameinfos = {"gamename" : extracted_game_name, "controllerconfig": gamepad_layout_file}
            json_gameinfos = json.dumps(gameinfos)
            log('placing game infos in temp for overlay: %s' % json_gameinfos)
            log('open : %s' % OVERLAY_GAME_RUNNING)
            gameinfo_file = open(OVERLAY_GAME_RUNNING, 'w')
            gameinfo_file.write(json_gameinfos)
            gameinfo_file.close()
            
            log('launching: %s' % execute_path)            
            subprocess.Popen(execute_path, shell=True, close_fds=True)
            kodiBusyDialog()
    except:
            log('ERROR: failed to launch: %s' % execute_path)
            print execute_path.encode('utf8')
    
    
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
		
# Play hoster link
def playable(params):
    log("g4gmanager.playable.media_url "+params.get("url"))
    plugintools.play_resolved_url( params.get("url") )    

# run the program
run()