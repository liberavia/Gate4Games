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
import urlparse
import urllib
import urllib2
import json
from qrcodewindow import ShowQrCodeDialog
import zipfile

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
API_BASE_URL = "http://www.gate4games.com/index.php?cl=gateosapi"
API_COMPATIBILITY_ATTRIBUTE = "&attributes=CompatibilityTypeLin--Ja"
YOUTUBE_API_KEY='AIzaSyDEJmWgKTSb8Gi4OUmWKY2YrLgI4pIbZQ0'
FREE_ROMS_BASE_URL='http://www.freeroms.com/'
FREE_ROMS_BASE_URL=FREE_ROMS_BASE_URL.encode('utf8')
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

def DownloaderClass(url,dest,title):
    dp = xbmcgui.DialogProgress()
    dp.create(language(50209).encode('utf8') + " " + title,language(50210).encode('utf8') + " " + title, url)
    urllib.urlretrieve(url,dest,lambda nb, bs, fs, url=url: _pbhook(nb,bs,fs,url,dp))
 
def _pbhook(numblocks, blocksize, filesize, url=None,dp=None):
    try:
        percent = min((numblocks*blocksize*100)/filesize, 100)
        print percent
        dp.update(percent)
    except:
        percent = 100
        dp.update(percent)
    if dp.iscanceled(): 
        print "DOWNLOAD CANCELLED" # need to get this part working
        dp.close()


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
    
    plugintools.add_item( action="dummy", title=language(50001).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="library_selection", title=language(50002).encode('utf-8') , thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="add_games", title=language(50003).encode('utf-8') , thumbnail=DEFAULT_THUMB , fanart=FANART , folder=True )
    plugintools.add_item( action="settings", title=language(50004).encode('utf-8') , thumbnail=SETTINGS_THUMB , fanart=FANART , folder=False )
    
# dummy message
def dummy(params):
    plugintools.message("Gate4Games Manager", language(59999).encode('utf8'),"")

# add games
def add_games(params):
    log("g4gmanager.add_games "+repr(params))
    
    plugintools.set_view(plugintools.THUMBNAIL)
    
    default_filter_and_sort = json.dumps(dict([('genre', ''), ('sortby', 'relevance'), ('sortdir', 'desc')]))
    
    plugintools.add_item( action="add_pc_games", title=language(50020).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART, extra=default_filter_and_sort, folder=True, page="1" )
    plugintools.add_item( action="dummy", title=language(50021).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="add_psx_games_letters", title=language(50022).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="dummy", title=language(50023).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="dummy", title=language(50024).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )

# choose game by letter    
def add_psx_games_letters(params):
    log("g4gmanager.add_psx_games_letters "+repr(params))
    
    plugintools.set_view(plugintools.LIST)
    
    free_roms_num       = FREE_ROMS_BASE_URL + "psx_roms_NUM.htm"
    free_roms_a         = FREE_ROMS_BASE_URL + "psx_roms_A.htm"
    free_roms_b         = FREE_ROMS_BASE_URL + "psx_roms_B.htm"
    free_roms_c         = FREE_ROMS_BASE_URL + "psx_roms_C.htm"
    free_roms_d         = FREE_ROMS_BASE_URL + "psx_roms_D.htm"
    free_roms_e         = FREE_ROMS_BASE_URL + "psx_roms_E.htm"
    free_roms_f         = FREE_ROMS_BASE_URL + "psx_roms_F.htm"
    free_roms_g         = FREE_ROMS_BASE_URL + "psx_roms_G.htm"
    free_roms_h         = FREE_ROMS_BASE_URL + "psx_roms_H.htm"
    free_roms_i         = FREE_ROMS_BASE_URL + "psx_roms_I.htm"
    free_roms_j         = FREE_ROMS_BASE_URL + "psx_roms_J.htm"
    free_roms_k         = FREE_ROMS_BASE_URL + "psx_roms_K.htm"
    free_roms_l         = FREE_ROMS_BASE_URL + "psx_roms_L.htm"
    free_roms_m         = FREE_ROMS_BASE_URL + "psx_roms_M.htm"
    free_roms_n         = FREE_ROMS_BASE_URL + "psx_roms_N.htm"
    free_roms_o         = FREE_ROMS_BASE_URL + "psx_roms_O.htm"
    free_roms_p         = FREE_ROMS_BASE_URL + "psx_roms_P.htm"
    free_roms_q         = FREE_ROMS_BASE_URL + "psx_roms_Q.htm"
    free_roms_r         = FREE_ROMS_BASE_URL + "psx_roms_R.htm"
    free_roms_s         = FREE_ROMS_BASE_URL + "psx_roms_S.htm"
    free_roms_t         = FREE_ROMS_BASE_URL + "psx_roms_T.htm"
    free_roms_u         = FREE_ROMS_BASE_URL + "psx_roms_U.htm"
    free_roms_v         = FREE_ROMS_BASE_URL + "psx_roms_V.htm"
    free_roms_w         = FREE_ROMS_BASE_URL + "psx_roms_W.htm"
    free_roms_x         = FREE_ROMS_BASE_URL + "psx_roms_X.htm"
    free_roms_y         = FREE_ROMS_BASE_URL + "psx_roms_X.htm"
    free_roms_z         = FREE_ROMS_BASE_URL + "psx_roms_Z.htm"
    
    plugintools.add_item( action="psx_letter_list", url=free_roms_num,   title="#", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_a,     title="A", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_b,     title="B", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_c,     title="C", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_d,     title="D", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_e,     title="E", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_f,     title="F", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_g,     title="G", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_h,     title="H", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_i,     title="I", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_j,     title="J", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_k,     title="K", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_l,     title="L", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_m,     title="M", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_n,     title="N", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_o,     title="O", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_p,     title="P", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_q,     title="Q", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_r,     title="R", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_s,     title="S", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_t,     title="T", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_u,     title="U", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_v,     title="V", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_w,     title="W", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_x,     title="X", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_y,     title="Y", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )
    plugintools.add_item( action="psx_letter_list", url=free_roms_z,     title="Z", thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )

# list games for letter    
def psx_letter_list(params):
    log("g4gmanager.psx_letter_list "+repr(params))
    
    url = params.get('url')
    
    body,response_headers = plugintools.read_body_and_headers(url)
    
    # only roms that provide images
    pattern = '<a href="([^"]+)">([^>]+)<img[^<]+'
    games = plugintools.find_multiple_matches(body,pattern)    
    
    for game in games:
        details_url = game[0]
        details_title = game[1]
        plugintools.add_item( action="psx_game_details", url=details_url,   title=details_title, thumbnail=DEFAULT_THUMB, fanart=FANART , folder=True )

def psx_game_details(params):
    log("g4gmanager.psx_letter_list "+repr(params))
    
    url = params.get('url')
    psx_title = params.get('title')
    
    body,response_headers = plugintools.read_body_and_headers(url)
    
    pattern = '&nbsp;<a href="([^"]+)">[^>]+'
    zip_source = plugintools.find_single_match(body,pattern)
    
    pattern = '<img src="([^"]+)" alt="[^"]+" width=300><BR></CENTER>[^>]+'
    cover_image = plugintools.find_single_match(body,pattern)

    plugintools.add_item( action="install_psx_game", url=zip_source,   title=language(50209).encode('utf8') + " " + psx_title, thumbnail=cover_image, fanart=cover_image , extra=psx_title, folder=False )
    
# downloads and installs psx game    
def install_psx_game(params):
    log("g4gmanager.install_psx_game "+repr(params))
    
    title = params.get('extra')
    thumbnail = params.get('thumbnail')
    
    # get work id
    next_id = get_next_game_id()
    
    # download zip
    download_source = params.get('url')
    target_path = HOME_DIR + "/.g4g/downloads/download_" + next_id + ".zip"
    DownloaderClass(download_source,target_path,title)
    
    
    # extract zip to target
    fh = open(target_path, 'rb')
    downloaded_zip = zipfile.ZipFile(fh)
    
    # search binfile and move to target
    xbmc.executebuiltin('Notification(' + title + ',' + language(50211).encode('utf8') + ' ' + title + ',2000,' + thumbnail + ')')
    xbmc.executebuiltin("ActivateWindow(busydialog)")
    for filename in downloaded_zip.namelist():
        log("g4gmanager.install_psx_game => extract => filename "+repr(filename))
        splitted_filename = filename.split('.')
        file_ending = splitted_filename[1]
        log("g4gmanager.install_psx_game => extract => file_ending "+repr(file_ending))
        if file_ending == 'bin':
            outfile_path = HOME_DIR + "/.g4g/roms/psx/"
            outfile_target_name = "rom_" + next_id + ".bin"
            log("g4gmanager.install_psx_game => extract => outfile_path "+repr(outfile_path))
            downloaded_zip.extract(filename, outfile_path)
            downloaded_zipfilename = filename
    fh.close()
    os.rename(outfile_path + downloaded_zipfilename, outfile_path + outfile_target_name)
    xbmc.executebuiltin("Dialog.Close(busydialog)") 
    
    # create startscript
    '''
    #!/bin/sh
    qjoypad Playstation1 &
    killall -9 kodi.bin
    pcsx -nogui -cdfile /home/steam/.g4g/roms/psx/tekken_3.bin
    killall -9 pcsx
    killall -9 qjoypad
    /usr/bin/kodi -fs
    '''
    script_filepath = HOME_DIR + "/.g4g/scripts/script_" + next_id
    target_scriptfile = open(script_filepath, 'w')
    
    target_scriptfile.write('#!/bin/sh')
    target_scriptfile.write("\n")
    target_scriptfile.write('qjoypad Playstation1 &')
    target_scriptfile.write("\n")
    target_scriptfile.write('killall -9 kodi.bin')
    target_scriptfile.write("\n")
    target_scriptfile.write('pcsx -nogui -cdfile ' + outfile_path + outfile_target_name)
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
    
    
    # create desktop file and cover
    create_desktop_file(title, 0, next_id, thumbnail, "")
    
    # cleanup
    os.remove(target_path)
    
def create_desktop_file(title,df_type,next_id,thumbnail,pc_type):
    script_filepath     = HOME_DIR + "/.g4g/scripts/script_" + next_id
    desktop_filepath    = HOME_DIR + "/.g4g/applications/game_" + next_id + ".desktop"
    icon_filepath       = HOME_DIR + "/.g4g/images/icons/icon_" + next_id + ".jpg"
    cover_filepath      = HOME_DIR + "/.g4g/images/cover/cover_" + next_id + ".jpg"
    fanart_filepath     = HOME_DIR + "/.g4g/images/fanart/fanart_" + next_id + ".jpg"
    
    type_switcher = {
        0: "psx",
        1: "ps2",
        2: "gc",
        3: "pc",
        4: "android",
    }

    pc_type_switcher = {
        0: "steam",
        1: "steam_wine",
        2: "gog",
        3: "gog_wine",
        4: "wine",
    }
    
    install_type = type_switcher.get(df_type, "none")
    pc_install_type = type_switcher.get(pc_type, "none")
    
    target_desktopfile = open(desktop_filepath, 'w')
    
    target_desktopfile.write('[Desktop Entry]')
    target_desktopfile.write("\n")
    target_desktopfile.write('Version=1.0')
    target_desktopfile.write("\n")
    target_desktopfile.write('Terminal=false')
    target_desktopfile.write("\n")
    target_desktopfile.write('Type='+ install_type)
    target_desktopfile.write("\n")
    target_desktopfile.write('PCType='+ pc_install_type)
    target_desktopfile.write("\n")
    target_desktopfile.write('Name=' + title + ' (' + install_type + ')')
    target_desktopfile.write("\n")
    target_desktopfile.write('Exec=' + script_filepath)
    target_desktopfile.write("\n")
    target_desktopfile.write('Icon=' + icon_filepath)
    target_desktopfile.close()
    
    # downloading cover and fanart
    log("g4gmanager.install_psx_game => image download from "+repr(thumbnail)+" to "+ cover_filepath)
    urllib.urlretrieve(thumbnail, cover_filepath)    
    urllib.urlretrieve(thumbnail, fanart_filepath)

# checks application dir iterate through all files and take the highest value add 1 and transfer it to 8 letter length string 
def get_next_game_id():
    highest_id = 0
    
    for filename in os.listdir(HOME_DIR + '/.g4g/scripts/'):
        splitted_filename = filename.split("_")
        current_id = splitted_filename[1]
        id_value = int(current_id)
        if id_value > highest_id:
            highest_id = id_value

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
    
    plugintools.add_item( action="library_installed", title=language(50010).encode('utf-8') , thumbnail=DEFAULT_THUMB , fanart=FANART , folder=True )
    plugintools.add_item( action="dummy", title=language(50011).encode('utf-8') , thumbnail=DEFAULT_THUMB , fanart=FANART , folder=False )
    
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
                if line.startswith('Type='):
                    game_type = line.replace('Type=', '')
                if line.startswith('Type='):
                    pc_game_type = line.replace('PCType=', '')

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
        if line.startswith('Name='):
            game_name = line.replace('Name=', '')
        if line.startswith('Type='):
            game_type = line.replace('Type=', '')
        if line.startswith('Type='):
            pc_game_type = line.replace('PCType=', '')

    current_file.close()
    plugintools.set_view(plugintools.LIST)
    
    start_app_caption = language(50212).encode('utf8') + " " + game_name.encode('utf-8')
    remove_app_caption = language(50213).encode('utf8') + " " + game_name.encode('utf-8')
    
    #actions
    plugintools.add_item( action="launch_app", title=start_app_caption , thumbnail=game_cover.encode('utf-8') , fanart=game_cover.encode('utf-8') , extra=execute_path, folder=False )
    plugintools.add_item( action="remove_app", title=remove_app_caption , thumbnail=game_cover.encode('utf-8') , fanart=game_cover.encode('utf-8') , extra=app_install_id, folder=False )
    
# remove app
def remove_app(params):
    dialog = xbmcgui.Dialog()
    remove_title = params.get('title')
    remove_message = language(50215).encode('utf8')
    if dialog.yesno(remove_title, remove_message):
        log("g4gmanager.remove_app => answer yes")
        xbmc.executebuiltin("ActivateWindow(busydialog)")

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
                        
        delete_path = None
        delete_file = None
        log("g4gmanager.remove_app => game_type: '" + game_type  + "'")        
        if game_type == 'psx':
            rom_psx_path = rom_base_path + 'psx/'
            log("g4gmanager.remove_app => rom_psx_path: " + rom_psx_path)        
            rom_filename = "rom_" + app_install_id + ".bin"
            delete_file = os.path.join(rom_psx_path,rom_filename)
            log("g4gmanager.remove_app => delete_file: " + delete_file)        
            

        log("g4gmanager.remove_app => delete_file: " + delete_file)        
        if delete_file is not None:
            os.remove(delete_file)
            os.remove(script_filepath)
            os.remove(desktop_filepath)
            os.remove(fanart_filepath)
            xbmc.executebuiltin('Notification(' + remove_title + ',' + remove_title + ' ' + language(50214).encode('utf8') + ',4000,' + cover_filepath + ')')
            os.remove(cover_filepath)
            
        if delete_path is not None:            
            log("g4gmanager.remove_app => delete_path: " + delete_path)        
            # delete complete path with all subfiles via shutil.rmtree() => will delete a directory and all its contents. 
            
        xbmc.executebuiltin("Dialog.Close(busydialog)")        
            
# launch app
def launch_app(params):
    execute_path = params.get('extra')
    game_name = params.get('title')
    try:
            log('attempting to launch: %s' % execute_path)
            print execute_path.encode('utf-8')
            subprocess.Popen(execute_path, shell=True, close_fds=True)
            kodiBusyDialog()
    except:
            log('ERROR: failed to launch: %s' % execute_path)
            print execute_path.encode(txt_encode)
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
		
# Play hoster link
def playable(params):
    log("g4gmanager.playable.media_url "+params.get("url"))
    plugintools.play_resolved_url( params.get("url") )    

# run the program
run()