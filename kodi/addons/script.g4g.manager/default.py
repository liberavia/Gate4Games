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
YOUTUBE_API_KEY='AIzaSyDEJmWgKTSb8Gi4OUmWKY2YrLgI4pIbZQ0';

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
    plugintools.add_item( action="dummy", title=language(50022).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="dummy", title=language(50023).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )
    plugintools.add_item( action="dummy", title=language(50024).encode('utf-8'), thumbnail=DEFAULT_THUMB, fanart=FANART , folder=False )

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
                    
                       
            plugintools.add_item( action="launch_game", title=game_name.encode('utf-8') , thumbnail = game_cover.encode('utf-8') , fanart=game_fanart.encode('utf-8') , extra=execute_path, folder=False )

# launch game
def launch_game(params):
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