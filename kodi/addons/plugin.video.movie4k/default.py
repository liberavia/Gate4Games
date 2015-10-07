# -*- coding: utf-8 -*-
#------------------------------------------------------------
# Movie4k
# Version 2.0.0
#------------------------------------------------------------
# License: GPL (http://www.gnu.org/licenses/gpl-3.0.html)
#------------------------------------------------------------

import os
import sys
import plugintools
import xbmcaddon
import datetime
import urlparse
import urllib
import urllib2
import pprint
import json
from moviedetails import MovieDetailsDialog

# some presets
addon = xbmcaddon.Addon()
THUMBNAIL_PATH = os.path.join( plugintools.get_runtime_path() , "resources" , "img" )
FANART = os.path.join( plugintools.get_runtime_path() , "fanart.jpg" )
plugintools.module_log_enabled = (plugintools.get_setting("debug")=="true")
plugintools.http_debug_log_enabled = (plugintools.get_setting("debug")=="true")

HOSTERS_ALLOWED = ["flashx","promptfi","filenuk","nosvide","divxsta","shares","mighty","putlock","nowvid","uploadc","zala","vidho","novamo","streamclo","videowee","socksha","primesha","played","vidbux","vidxden","clicktov","stagevu","vidstream","movreel","hugefiles","180upload","megarelease","lemuploads","epicshare","2shared","youtube","vimeo","movpod","gorillavid","daclips"]

YOUTUBE_API_KEY='AIzaSyDEJmWgKTSb8Gi4OUmWKY2YrLgI4pIbZQ0';

if plugintools.get_setting("use_proxy")=="true":
    # Using a proxy can be nessessary due there are ssl problems in within compiled python versions of kodi. Need of having python >= 2.7.9 is often not possible.
    proxy_url           = plugintools.get_setting("proxy_url")
    proxy_domain        = proxy_url.replace("https://","")
    proxy_domain        = proxy_domain.replace("http://","")

    if proxy_domain.endswith("/"):
        # means that proxy url had mandatory ending slash but it needs to be removed from domain string
        proxy_domain = proxy_domain[:-1]
    else:
        # means proxy url had no ending slash which is mandatory
        proxy_url = proxy_url + "/"
        
    MAIN_URL = proxy_url
    SITE_DOMAIN = proxy_domain
else:
    MAIN_URL = "https://www.movie4k.to/"
    SITE_DOMAIN = "www.movie4k.to"
    
plugintools.log("Requesting URL: " + MAIN_URL + " which contains the Domain: " + SITE_DOMAIN)    

# language constants

# MAIN MENU
LANG_TITLE_MAIN_MOVIES=addon.getLocalizedString(30020).encode('utf-8')
LANG_TITLE_MAIN_TVSHOWS=addon.getLocalizedString(30021).encode('utf-8')
LANG_TITLE_MAIN_XXX=addon.getLocalizedString(30022).encode('utf-8')
LANG_TITLE_MAIN_SEARCH=addon.getLocalizedString(30023).encode('utf-8')
LANG_TITLE_MAIN_PREFERENCES=addon.getLocalizedString(30024).encode('utf-8')
# Movies
LANG_TITLE_MOVIES_CINEMA=addon.getLocalizedString(30025).encode('utf-8')
LANG_TITLE_MOVIES_LATEST=addon.getLocalizedString(30026).encode('utf-8')
LANG_TITLE_MOVIES_ALL=addon.getLocalizedString(30027).encode('utf-8')
LANG_TITLE_MOVIES_GENRES=addon.getLocalizedString(30028).encode('utf-8')
# TV-Shows
LANG_TITLE_TVSHOWS_FEATURED=addon.getLocalizedString(30029).encode('utf-8')
LANG_TITLE_TVSHOWS_LATEST=addon.getLocalizedString(30030).encode('utf-8')
LANG_TITLE_TVSHOWS_ALL=addon.getLocalizedString(30031).encode('utf-8')
LANG_TITLE_TVSHOWS_GENRES=addon.getLocalizedString(30032).encode('utf-8')
# Quality
LANG_QUALITY=addon.getLocalizedString(30033).encode('utf-8')
LANG_QUALITY_0=addon.getLocalizedString(30034).encode('utf-8')
LANG_QUALITY_1=addon.getLocalizedString(30035).encode('utf-8')
LANG_QUALITY_2=addon.getLocalizedString(30036).encode('utf-8')
LANG_QUALITY_3=addon.getLocalizedString(30037).encode('utf-8')
LANG_QUALITY_4=addon.getLocalizedString(30038).encode('utf-8')
LANG_QUALITY_5=addon.getLocalizedString(30039).encode('utf-8')
# Languages
LANG_ENGLISH=addon.getLocalizedString(30100).encode('utf-8')
LANG_GERMAN=addon.getLocalizedString(30101).encode('utf-8')
LANG_SPANISH=addon.getLocalizedString(30102).encode('utf-8')
LANG_GREEK=addon.getLocalizedString(30103).encode('utf-8')
LANG_TURK=addon.getLocalizedString(30104).encode('utf-8')
LANG_RUSSIAN=addon.getLocalizedString(30105).encode('utf-8')
LANG_JAPANESE=addon.getLocalizedString(30106).encode('utf-8')
LANG_FRENCH=addon.getLocalizedString(30107).encode('utf-8')
# MISC
LANG_PUBLISHED_AT=addon.getLocalizedString(30040).encode('utf-8')
LANG_PLAY_VIDEO_FROM=addon.getLocalizedString(30041).encode('utf-8')
LANG_VIDEO_NOT_PLAYABLE=addon.getLocalizedString(30042).encode('utf-8')
LANG_HOSTER_DOWN=addon.getLocalizedString(30043).encode('utf-8')
LANG_SELECT_OTHER_OPTION=addon.getLocalizedString(30044).encode('utf-8')
LANG_NO_VALID_LINKS_FOUND=addon.getLocalizedString(30045).encode('utf-8')
LANG_ERROR_READING_DATA_M4K=addon.getLocalizedString(30046).encode('utf-8')
LANG_ALTENATIVE_LINK_FOUND=addon.getLocalizedString(30047).encode('utf-8')
LANG_EPISODE=addon.getLocalizedString(30048).encode('utf-8')
LANG_SEASON=addon.getLocalizedString(30049).encode('utf-8')
# DETAILS
LANG_DETAILS_TITLE_PREFIX=addon.getLocalizedString(30053).encode('utf-8')
LANG_DETAILS_GENRE=addon.getLocalizedString(30052).encode('utf-8')
LANG_DETAILS_LENGTH=addon.getLocalizedString(30054).encode('utf-8')
LANG_DETAILS_LANDYEAR=addon.getLocalizedString(30055).encode('utf-8')
LANG_DETAILS_REGIE=addon.getLocalizedString(30056).encode('utf-8')
LANG_DETAILS_ACTORS=addon.getLocalizedString(30057).encode('utf-8')
LANG_MOVIES=addon.getLocalizedString(30059).encode('utf-8')
LANG_SEARCH_TRAILERS_FOR_1=addon.getLocalizedString(30060).encode('utf-8')
LANG_SEARCH_TRAILERS_FOR_2=addon.getLocalizedString(30061).encode('utf-8')
LANG_FSK=addon.getLocalizedString(30062).encode('utf-8')
LANG_FSK_NOT_AVALABLE=addon.getLocalizedString(30063).encode('utf-8')
LANG_FSK_NOT_YET_RATED=addon.getLocalizedString(30064).encode('utf-8')
LANG_FSK_FROM=addon.getLocalizedString(30065).encode('utf-8')
LANG_FSK_YEARS=addon.getLocalizedString(30066).encode('utf-8')
LANG_LIST_MAIN_ACTORS=addon.getLocalizedString(30067).encode('utf-8')

# language related params (currently only english and german)
# @todo: add all other available languages
if plugintools.get_setting('movie4k_language') == '1':
  LANG_PARAM = "?lang=en"
elif plugintools.get_setting('movie4k_language') == '2':  
  LANG_PARAM = "?lang=de"
else:
  LANG_PARAM = ""


######################################################################################## MAIN SECTION ##############################################################################################


# Entry point
def run():
    plugintools.log("movie4k.run")
    
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
    plugintools.log("movie4k.main_list "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)

    plugintools.add_item( action="movies",    title=LANG_TITLE_MAIN_MOVIES , thumbnail = os.path.join(THUMBNAIL_PATH,"movies.png") , fanart=FANART , folder=True )
    plugintools.add_item( action="tvshows",   title=LANG_TITLE_MAIN_TVSHOWS , thumbnail = os.path.join(THUMBNAIL_PATH,"tvshows.png") , fanart=FANART , folder=True )
    if plugintools.get_setting("show_adult")=="true":
        plugintools.add_item( action="xxxmovies", title=LANG_TITLE_MAIN_XXX , thumbnail = os.path.join(THUMBNAIL_PATH,"xxx.png") , fanart=FANART , folder=True )
    plugintools.add_item( action="search",    title=LANG_TITLE_MAIN_SEARCH , thumbnail = os.path.join(THUMBNAIL_PATH,"search.png") , fanart=FANART , folder=True )
    plugintools.add_item( action="settings",  title=LANG_TITLE_MAIN_PREFERENCES , thumbnail = os.path.join(THUMBNAIL_PATH,"settings.png") , fanart=FANART , folder=False )

# Settings dialog
def settings(params):
    plugintools.log("movie4k.settings "+repr(params))

    plugintools.open_settings_dialog()

    if plugintools.get_setting("force_advancedsettings")=="true":
        # advancedsettings.xml path
        import xbmc,xbmcgui,os
        advancedsettings = xbmc.translatePath("special://userdata/advancedsettings.xml")

        if not os.path.exists(advancedsettings):
            # copy advancedsettings.xml from resources to userdata
            fichero = open( os.path.join(plugintools.get_runtime_path(),"resources","advancedsettings.xml") )
            text = fichero.read()
            fichero.close()
            
            fichero = open(advancedsettings,"w")
            fichero.write(text)
            fichero.close()

            plugintools.message("movie4k", "A new file userdata/advancedsettings.xml","has been created for optimal streaming")

# Search
def search(params):
    plugintools.log("movie4k.search "+repr(params))
    text = plugintools.keyboard_input(title="Input search terms")

    url = MAIN_URL+"movies.php?list=search"
    post = "search="+text.replace(" ","+")

    body,response_headers = read_body_and_headers(url, post=post)
    pattern  = '<TR id="coverPreview[^<]+'
    pattern += '<TD width="550" id="tdmovies"[^<]+'
    pattern += '<a href="([^"]+)">([^<]+)</a>'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedtitle in matches:
        
        url = urlparse.urljoin(url,scrapedurl)
        title = scrapedtitle.strip()
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.search title="+title+", url="+url+", thumbnail="+thumbnail)

        if "watch-tvshow" in url:
            url = MAIN_URL+"tvshows-season-"+plugintools.find_single_match(url,MAIN_URL+"([A-Za-z0-9\-]+)-watch-tvshow-\d+.html")+".html"
            plugintools.add_item( action="tvshow_seasons", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )
        else:
            plugintools.add_item( action="single_movie", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

# Movies
def movies(params):
    plugintools.log("movie4k.movies "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)

    plugintools.add_item( action="movies_cinema",    title=LANG_TITLE_MOVIES_CINEMA , thumbnail = os.path.join(THUMBNAIL_PATH,"movies_cinema.png") , fanart=FANART, url=MAIN_URL+"index.php" + LANG_PARAM, folder=True )
    plugintools.add_item( action="letters",          title=LANG_TITLE_MOVIES_ALL , thumbnail = os.path.join(THUMBNAIL_PATH,"movies_az.png") , fanart=FANART, extra="movies-all", url=MAIN_URL+"movies-all.html" + LANG_PARAM, folder=True )
    plugintools.add_item( action="genres",           title=LANG_TITLE_MOVIES_GENRES , thumbnail = os.path.join(THUMBNAIL_PATH,"movies_genre.png") , fanart=FANART, extra="movies-genre", url=MAIN_URL+"genres-movies.html" + LANG_PARAM, folder=True )

# TV Shows
def tvshows(params):
    plugintools.log("movie4k.tvshows "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)

    plugintools.add_item( action="tvshows_featured",  title=LANG_TITLE_TVSHOWS_FEATURED , thumbnail = os.path.join(THUMBNAIL_PATH,"tvshows_featured.png") , fanart=FANART , folder=True , url=MAIN_URL+'tvshows_featured.php' + LANG_PARAM )
    plugintools.add_item( action="tvshow_episodes",   title=LANG_TITLE_TVSHOWS_LATEST , thumbnail = os.path.join(THUMBNAIL_PATH,"tvshows_latest.png") , fanart=FANART , folder=True , url=MAIN_URL+'tvshows-updates.html' + LANG_PARAM )
    plugintools.add_item( action="letters",           title=LANG_TITLE_TVSHOWS_ALL , thumbnail = os.path.join(THUMBNAIL_PATH,"tvshows_az.png") , fanart=FANART , folder=True , extra="tvshows-all", url = MAIN_URL+'tvshows-all.html' + LANG_PARAM )
    plugintools.add_item( action="genres",            title=LANG_TITLE_TVSHOWS_GENRES , thumbnail = os.path.join(THUMBNAIL_PATH,"tvshows_genre.png") , fanart=FANART , folder=True , extra="tvshows-genre", url = MAIN_URL+'genres-tvshows.html' + LANG_PARAM )

# XXX Movies
def xxxmovies(params):
    plugintools.log("movie4k.xxxmovies "+repr(params))

    plugintools.set_view(plugintools.THUMBNAIL)

    plugintools.add_item( action="xxx_movies_updates", title="Latest updates" , thumbnail = os.path.join(THUMBNAIL_PATH,"xxx.png") , fanart=FANART , folder=True , url=MAIN_URL+'xxx-updates.html' + LANG_PARAM )
    plugintools.add_item( action="letters",            title="All movies" , thumbnail = os.path.join(THUMBNAIL_PATH,"xxx.png") , fanart=FANART , folder=True , extra="xxx-all", url=MAIN_URL+'xxx-all.html' + LANG_PARAM )
    plugintools.add_item( action="genres",             title="Genres" , thumbnail = os.path.join(THUMBNAIL_PATH,"xxx.png") , fanart=FANART , folder=True , extra="xxx-genre", url=MAIN_URL+'genres-xxx.html' + LANG_PARAM )

# Cinema movies
def movies_cinema(params):
    plugintools.log("movie4k.movies_cinema "+repr(params))

    #plugintools.set_view(plugintools.MOVIES)

    body,response_headers = read_body_and_headers(params.get("url"))
    pattern  = '<div style="float.left"[^<]+'
    pattern += '<a href="([^"]+)"><img src="([^"]+)".*?'
    pattern += '<h2[^<]+<a[^<]+<font[^<]+</a[^<]+<img src="([^"]+)".*?'
    pattern += 'IMDB Rating: <a href="[^"]+" target="_blank">([^<]+)</a>.*?'
    pattern += '<span style="font-size:14px;vertical-align:top;">Quality: <img src="([^"]+)".*?'
    pattern += '<div id="info\d+"[^<]+<STRONG>([^<]+)</STRONG><BR>([^<]+)</div>'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedthumbnail, flag, imdb_rating, quality, scrapedtitle, scrapedplot in matches:

        quality_gif = quality.replace( "/img/smileys/", "" );
        quality = fetch_quality_by_gif(quality_gif)
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle
        if title.strip().endswith(":"):
            title = title.strip()[:-1]
        title=title + " (IMDB:" + imdb_rating + ") (" + LANG_QUALITY + quality + ") " + get_language_from_flag_img(flag)
        thumbnail = urlparse.urljoin(params.get("url"),scrapedthumbnail)
        thumbnail = thumbnail.replace("img.movie4k.tv//","img.movie4k.tv/")
        thumbnail = thumbnail.replace("https://","http://")
        plot = scrapedplot
        plugintools.log("movie4k.movies_cinema title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="single_movie", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True, quality=quality, imdb_rating=imdb_rating )

    pattern = '<div id="maincontent2"[^<]+<div style="float: left;"><a href="([^"]+)"><img src="([^"]+)" alt="([^"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedthumbnail, scrapedtitle in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle
        thumbnail = urlparse.urljoin(params.get("url"),scrapedthumbnail)
        thumbnail = thumbnail.replace("img.movie4k.tv//","img.movie4k.tv/")
        thumbnail = thumbnail.replace("https://","http://")
        
        plugintools.log("movie4k.movies_cinema title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="single_movie", title=title, url=url, thumbnail=thumbnail , fanart=thumbnail , folder=True )

# Latest updates
def movies_updates(params):
    plugintools.log("movie4k.movies_updates "+repr(params))

    #plugintools.set_view(plugintools.LIST)

    body,response_headers = read_body_and_headers(params.get("url"))

    pattern  = '<TR id="coverPreview[^<]+'
    pattern += '<TD width="550" id="tdmovies"[^<]+'
    pattern += '<a href="([^"]+)">([^<]+)</a[^<]+.*?<img border=0 src="([^"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedtitle, flag in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        if title.strip().endswith(":"):
            title = title.strip()[:-1]
        title=title + get_language_from_flag_img(flag)
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.movies_updates title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="single_movie", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

# Latest updates
def xxx_movies_updates(params):
    plugintools.log("movie4k.xxx_movies_updates "+repr(params))

    #plugintools.set_view(plugintools.LIST)

    body,response_headers = read_body_and_headers(params.get("url"))

    pattern  = '<div style="float. left.">'
    pattern += '<a href="([^"]+)"><img src="([^"]+)" alt="([^"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedthumbnail, scrapedtitle in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        thumbnail = urlparse.urljoin(params.get("url"),scrapedthumbnail)
        thumbnail = thumbnail.replace("img.movie4k.tv//","img.movie4k.tv/")
        thumbnail = thumbnail.replace("https://","http://")
        plot = ""
        plugintools.log("movie4k.xxx_movies_updates title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="single_movie", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

# All movies by letter
def letters(params):
    plugintools.log("movie4k.letters "+repr(params))

    #plugintools.set_view(plugintools.LIST)

    body,response_headers = read_body_and_headers(params.get("url"))

    #<div id="boxgrey"><a href=MAIN_URL+"/tvshows-all-G.html">
    #<div id="boxgrey"><a href="./xxx-all-N.html">N</a> 
    #<div id="boxgrey"><a href="./movies-all-O.html">O</a>
    pattern  = '<div id="boxgrey"><a href="(./'+params.get("extra")+'[^"]+)">([^<]+)</a>'
    matches = plugintools.find_multiple_matches(body,pattern)

    plugintools.add_item( action="movies_all", title="#", thumbnail="" , plot="", fanart="", url=MAIN_URL+""+params.get("extra")+"-1.html", folder=True )

    for scrapedurl, scrapedtitle in matches:
        url = MAIN_URL+scrapedurl
        title = scrapedtitle.strip()
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.letters title="+title+", url="+url+", thumbnail="+thumbnail)

        if params.get("extra")=="tvshows-all":
            plugintools.add_item( action="tvshows_all", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )
        else:
            plugintools.add_item( action="movies_all", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

    #<div id="boxgrey"><a href=MAIN_URL+"/tvshows-all-G.html">
    pattern  = '<div id="boxgrey"><a href="(https\://'+SITE_DOMAIN+'//'+params.get("extra")+'-[^"]+)">([^<]+)</a>'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedtitle in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.letters title="+title+", url="+url+", thumbnail="+thumbnail)

        if params.get("extra")=="tvshows-all":
            plugintools.add_item( action="tvshows_all", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )
        else:
            plugintools.add_item( action="movies_all", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

# All movies by letter
def movies_all(params):
    plugintools.log("movie4k.movies_all "+repr(params))

    #plugintools.set_view(plugintools.THUMBNAIL)

    body,response_headers = read_body_and_headers(params.get("url"))
    pattern  = '<TR id="(coverPreview\d+)[^<]+'
    pattern += '<TD width="550" id="tdmovies"[^<]+'
    pattern += '<a href="([^"]+)">([^<]+)</a>[^>]+.*?'
    pattern += '<TD width="25" id="tdmovies">[^<]+<img src="([^\"]+)" border=0>[^>]+.*?'
    pattern += '<TD id="tdmovies" width="114">[^>]+<STRONG>([^>]+)</STRONG>[^>]+.*?'
    pattern += '<TD align="right" id="tdmovies"[^<]+<img border=0 src="([^\"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)

    for cover_id, scrapedurl, scrapedtitle, quality, imdb_rating, flag in matches:
        
        quality_gif = quality.replace( "/img/smileys/", "" );
        quality = fetch_quality_by_gif(quality_gif)
        imdb_rating = imdb_rating.strip()
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        if title.strip().endswith(":"):
            title = title.strip()[:-1]
        title=title + " (" + imdb_rating + ") (" + LANG_QUALITY + quality + ") " + get_language_from_flag_img(flag)
        thumbnail = plugintools.find_single_match(body,"\$\(\"\#"+cover_id+"\"\).hover\(function\(e\)[^<]+<p id='coverPreview'><img src='([^']+)'")
        thumbnail = thumbnail.replace("img.movie4k.tv//","img.movie4k.tv/")
        thumbnail = thumbnail.replace("https://","http://")
        plot = ""
        plugintools.log("movie4k.movies_all title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="single_movie", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True, quality=quality, imdb_rating=imdb_rating )

    next_page_url = plugintools.find_single_match(body,'<div id="boxwhite">\d+ </div><div id="boxgrey"><a href="([^"]+)">\d+')
    next_page_number = plugintools.find_single_match(body,'<div id="boxwhite">\d+ </div><div id="boxgrey"><a href="[^"]+">(\d+)')
    if next_page_url!="":
        plugintools.add_item( action="movies_all", title=">> Go to page "+next_page_number, url=urlparse.urljoin(params.get("url"),next_page_url), folder=True )

# Movie genres
def genres(params):
    plugintools.log("movie4k.genres "+repr(params))

    #plugintools.set_view(plugintools.LIST)

    body,response_headers = read_body_and_headers(params.get("url"))

    '''
    <TR>
    <TD id="tdmovies" width="155"><a href="movies-genre-59-Reality-TV.html">Reality-TV</a></TD>
    <TD id="tdmovies" width="175">30</TD>
    <TD id="tdmovies" width="155"><a href="/tipp-film-genre-59.html">Filmtipp</a></TD>
    </TR>
    '''
    pattern  = '<TR[^<]+'
    pattern += '<TD id="tdmovies" width="\d+"><a href="('+params.get("extra")+'-[^"]+)">([^<]+)</a></TD[^<]+'
    pattern += '<TD id="tdmovies" width="\d+">(\d+)</TD[^<]+'
    if params.get("extra") == "tvshows-genre":
        pattern += '<TD id="tdmovies" width="\d+"><a href="/'+'tipp-show-genre'+'-[^"]+">[^<]+</a></TD[^<]+'
    else:
        pattern += '<TD id="tdmovies" width="\d+"><a href="/'+'tipp-film-genre'+'-[^"]+">[^<]+</a></TD[^<]+'
    pattern += '</TR>'
    matches = plugintools.find_multiple_matches(body,pattern)
    
    #?order=imdbrating
    if plugintools.get_setting("sort_imdb")=="true":
        defaultsorting = "?order=imdbrating"
    else:
        defaultsorting = ""

    for scrapedurl, scrapedtitle, counter in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl + defaultsorting)
        title = scrapedtitle.strip()+" ("+counter+" " + LANG_MOVIES + ")"
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.genres title="+title+", url="+url+", thumbnail="+thumbnail)

        if params.get("extra") == "tvshows-genre":
            plugintools.add_item( action="tvshows_all", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )
        else:
            plugintools.add_item( action="movies_all", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

# Featured tv shows
def tvshows_featured(params):
    plugintools.log("movie4k.tvshows_featured "+repr(params))

    #plugintools.set_view(plugintools.MOVIES)

    body,response_headers = read_body_and_headers(params.get("url"))
    pattern  = '<div style="float.left"[^<]+'
    pattern += '<a href="([^"]+)"><img src="([^"]+)".*?'
    pattern += '<h2[^<]+<a[^<]+<font[^>]+>([^<]+)</a[^<]+<img src="([^"]+)".*?'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedthumbnail, scrapedtitle, flag in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        # url = MAIN_URL+"tvshows-season-"+plugintools.find_single_match(url,MAIN_URL+"([A-Za-z0-9\-]+)-watch-tvshow-\d+.html")+".html"

        title = scrapedtitle
        if title.strip().endswith(":"):
            title = title.strip()[:-1]
        title=title + get_language_from_flag_img(flag)
        thumbnail = urlparse.urljoin(params.get("url"),scrapedthumbnail)
        thumbnail = thumbnail.replace("img.movie4k.tv//","img.movie4k.tv/")
        thumbnail = thumbnail.replace("https://","http://")
        plot = ""
        plugintools.log("movie4k.tvshows_featured title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="tvshow_detailspage", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

# All tv shows by letter
def tvshows_all(params):
    plugintools.log("movie4k.tvshows_all "+repr(params))

    #plugintools.set_view(plugintools.THUMBNAIL)

    '''
    <TR>
    <TD id="tdmovies" width="538"><a href="tvshows-season-Jane-by-Design.html">Jane By Design                                   </a></TD>
    <TD id="tdmovies"><img border=0 src="http://img.movie4k.to/img/us_flag_small.png" width=24 height=14></TD>
    </TR>
    '''

    body,response_headers = read_body_and_headers(params.get("url"))
    pattern  = '<TR[^<]+'
    pattern += '<TD id="tdmovies" width="538"[^<]+'
    pattern += '<a href="([^"]+)">([^<]+)</a.*?<img border=0 src="([^\"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedtitle, flag in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        if title.strip().endswith(":"):
            title = title.strip()[:-1]
        title=title + get_language_from_flag_img(flag)
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.tvshows_all title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="tvshow_filter_detailspage", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

    next_page_url = plugintools.find_single_match(body,'<div id="boxwhite">\d+ </div><div id="boxgrey"><a href="([^"]+)">\d+')
    next_page_number = plugintools.find_single_match(body,'<div id="boxwhite">\d+ </div><div id="boxgrey"><a href="[^"]+">(\d+)')
    if next_page_url!="":
        plugintools.add_item( action="tvshows_all", title=">> Go to page "+next_page_number, url=urlparse.urljoin(params.get("url"),next_page_url), folder=True )

# TV Show detailspage
def tvshow_detailspage(params):
    pp = pprint.PrettyPrinter(indent=4)
    plugintools.log("movie4k.tvshow_seasons "+repr(params))

    #plugintools.set_view(plugintools.LIST)

    body,response_headers = read_body_and_headers(params.get("url"))
    '''
    <div id="episodediv1" style="display:none">
    <FORM name="episodeform1">
	<SELECT name="episode" style="margin-left:18px;" onChange="gotoEpisode(this.value);">
	    <OPTION></OPTION>
	    <OPTION value="tvshows-5239616-The-Big-Bang-Theory.html">Episode 1</OPTION><OPTION value="tvshows-5239591-The-Big-Bang-Theory.html">Episode 2</OPTION><OPTION value="tvshows-5239588-The-Big-Bang-Theory.html">Episode 3</OPTION><OPTION value="tvshows-5239641-The-Big-Bang-Theory.html">Episode 4</OPTION><OPTION value="tvshows-5239611-The-Big-Bang-Theory.html" selected>Episode 5</OPTION><OPTION value="tvshows-5239560-The-Big-Bang-Theory.html">Episode 6</OPTION><OPTION value="tvshows-5239628-The-Big-Bang-Theory.html">Episode 7</OPTION><OPTION value="tvshows-5239671-The-Big-Bang-Theory.html">Episode 8</OPTION><OPTION value="tvshows-5239631-The-Big-Bang-Theory.html">Episode 9</OPTION><OPTION value="tvshows-5239567-The-Big-Bang-Theory.html">Episode 10</OPTION><OPTION value="tvshows-5239592-The-Big-Bang-Theory.html">Episode 11</OPTION><OPTION value="tvshows-5239564-The-Big-Bang-Theory.html">Episode 12</OPTION><OPTION value="tvshows-5239620-The-Big-Bang-Theory.html">Episode 13</OPTION><OPTION value="tvshows-5239601-The-Big-Bang-Theory.html">Episode 14</OPTION><OPTION value="tvshows-5239599-The-Big-Bang-Theory.html">Episode 15</OPTION><OPTION value="tvshows-5239604-The-Big-Bang-Theory.html">Episode 16</OPTION><OPTION value="tvshows-5239562-The-Big-Bang-Theory.html">Episode 17</OPTION>                            
	</SELECT>
    </FORM>
    </div>
    '''
    
    # First get the season parts
    pattern = '<div id="episodediv([0-9])" style="[^<]+'
    seasons = plugintools.find_multiple_matches(body,pattern)
    seasons_string = str(pp.pprint(seasons))
    plugintools.log("movie4k.tvshow_seasons "+seasons_string)
    
    for season in seasons:
        plugintools.log("movie4k.tvshow_seasons fetching episodes from season "+season)
        pattern = '<FORM name="episodeform'+str(season)+'">(.*?)</FORM>'
        content_part = plugintools.find_single_match(body,pattern)
        # plugintools.log("movie4k.tvshow_seasons \n"+content_part)
        pattern = '<OPTION value="([^\"]+)"[^</SELECT]+'
        matches = plugintools.find_multiple_matches(content_part,pattern)
        matches_string = str(pp.pprint(matches))
        episodenr = 0
        for episodeurl in matches:
            episodenr += 1
            url = urlparse.urljoin(params.get("url"),episodeurl)
            title=LANG_SEASON + " " + str(season) + ", " + LANG_EPISODE + " " + str(episodenr)
            thumbnail = ""
            plot = ""
            plugintools.log("movie4k.tvshow_seasons title="+title+", url="+url+", thumbnail="+thumbnail)

            plugintools.add_item( action="single_tvshow", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )
            
# TV Show seasons
def tvshow_seasons(params):
    plugintools.log("movie4k.tvshow_seasons "+repr(params))

    #plugintools.set_view(plugintools.LIST)

    body,response_headers = read_body_and_headers(params.get("url"))

    '''
    <TR>
    <TD id="tdmovies" width="538"><a href="tvshows-episode-1-Arrow.html">Arrow          , Season: 1                     </a></TD>
    <TD id="tdmovies"><img border=0 src="http://img.movie4k.to/img/us_ger_small.png" width=24 height=14></TD>
    </TR>
    '''
    pattern  = '<TR[^<]+'
    pattern += '<TD id="tdmovies" width="\d+"><a href="([^"]+)">([^<]+)</a></TD[^<]+'
    pattern += '<TD id="tdmovies"><img border=0 src="([^\"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedtitle, flag in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        title=title + get_language_from_flag_img(flag)
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.tvshow_seasons title="+title+", url="+url+", thumbnail="+thumbnail)

        plugintools.add_item( action="tvshow_episodes", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )


# TV Show filter details page
def tvshow_filter_detailspage(params):
    plugintools.log("movie4k.tvshow_fetch_detailspage "+repr(params))

    
    # exchange found url to be able to got directly to episodes page wher first entry can be passed to tvshow_detailspage
    url = params.get("url")
    url = url.replace("season","episode-1")
    # get resulting episodes page where first hit is the url we're passing to tvshow_detailspage
    body,response_headers = read_body_and_headers(url)
    
    pattern  = '<TR[^<]+'
    pattern += '<TD id="tdmovies" width="\d+"[^<]+'
    pattern += '<a href="([^"]+)">([^<]+)</a></TD[^<]+'
    pattern += '<TD id="tdmovies" width="\d+"[^<]+'
    pattern += '<img[^>]+>([^<]+)</TD[^<]+'
    pattern += '.*?<img border=0 src="([^"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)
    
    for scrapedurl, scrapedtitle, mirrorname, flag in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        if title.strip().endswith(":"):
            title = title.strip()[:-1]            
        title = title + " ("+mirrorname.strip().replace("watch on ","")+")"
        title=title + get_language_from_flag_img(flag)
        dicttitle = title.split(" ")
        for titlepart in dicttitle:
            title = titlepart
            break
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.tvshow_filter_detailspage="+title+", url="+url+", thumbnail="+thumbnail)
        break
    
    action="tvshow_detailspage"
    extra=""
    page=""
    quality=""
    imdb_rating=""
    comments=""
    movie_language=""
    genre=""
    actorsandmore=""
    landyear=""
    
    myparams = {'action': action, 'title': title, 'url': url, 'thumbnail': thumbnail, 'plot': plot, 'extra': extra, 'page': page, 'quality': quality, 'imdb_rating': imdb_rating, 'comments': comments, 'movie_language': movie_language, 'genre': genre, 'actorsandmore': actorsandmore, 'landyear': landyear}
    
    tvshow_detailspage(myparams)
    pass

    
  
# Latest updates
def tvshow_episodes(params):
    plugintools.log("movie4k.tvshow_episodes "+repr(params))

    #plugintools.set_view(plugintools.LIST)
    '''
    <TR>
    <TD id="tdmovies" width="538"><a href="Arrow-watch-tvshow-3334157.html">Arrow           , Season: 1         , Episode: 22           </a></TD>
    <TD id="tdmovies" width="182">
    <img src="http://img.movie4k.to/img/question.png" width=16> watch on N/A            </TD>
    <TD id="tdmovies" width="25">&nbsp;</TD>
    <TD id="tdmovies" width="175">06/08/2013 09:03 am</TD>
    <TD id="tdmovies"><img border=0 src="http://img.movie4k.to/img/us_flag_small.png" width=24 height=14></TD>
    </TR>
    '''
    body,response_headers = read_body_and_headers(params.get("url"))

    pattern  = '<TR[^<]+'
    pattern += '<TD id="tdmovies" width="\d+"[^<]+'
    pattern += '<a href="([^"]+)">([^<]+)</a></TD[^<]+'
    pattern += '<TD id="tdmovies" width="\d+"[^<]+'
    pattern += '<img[^>]+>([^<]+)</TD[^<]+'
    pattern += '.*?<img border=0 src="([^"]+)"'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedtitle, mirrorname, flag in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip()
        if title.strip().endswith(":"):
            title = title.strip()[:-1]
        title = title + " ("+mirrorname.strip().replace("watch on ","")+")"
        title=title + get_language_from_flag_img(flag)
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.tvshow_episodes title="+title+", url="+url+", thumbnail="+thumbnail)

        for hoster in HOSTERS_ALLOWED:
            #plugintools.log("<<<<<"+hoster+">>>>> IN <<<<<<"+title.lower()+">>>>>>")
            if hoster in title.lower():
                plugintools.add_item( action="play", title=title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )

# Show tv-shows links
def single_tvshow(params):
    pp = pprint.PrettyPrinter(indent=4)
    plugintools.log("movie4k.single_tvshow "+repr(params))

    #plugintools.set_view(plugintools.LIST)
    found = False

    body,response_headers = read_body_and_headers(params.get("url"))
    body = body.replace("\\\"","\"")
    # response_headers_string = str(pp.pprint(response_headers))
    redirect_url = ''
    for key, value in response_headers:
        if key == 'location' and value != '':
            redirect_url = value
    
    if redirect_url != '':
        body,response_headers = read_body_and_headers(MAIN_URL+redirect_url)
        body = body.replace("\\\"","\"")
        
    # plugintools.log("movie4k.single_tvshow body:\n"+body+"\n")

    '''
        <tr id=\"tablemoviesindex2\" onClick=\"window.location.href = 'tvshows-5807835-The-Walking-Dead.html';return false;\" style=\"cursor:hand;cursor:pointer;\"><td style=\"padding-left:5px;height:20px;width:80px;\"><a href=\"tvshows-5807835-The-Walking-Dead.html\">Episode 7</a>&nbsp;</td><td align=\"left\" width=\"150\"><a href=\"tvshows-5807835-The-Walking-Dead.html\" style=\"margin-left:18px;\"><img border=0 style=\"vertical-align:top;\" src=\"/img/hoster/178.png\" alt=\"Streamcloud The Walking Dead\" title=\"Streamcloud The Walking Dead\" width=\"16\"> &nbsp;Streamcloud</a></td></tr>
    '''

    pattern  = "window.location.href = '([^']+)'[^']+"
    pattern += 'width=\\"16\\"> &nbsp;([^;]+)</a>'
    matches = plugintools.find_multiple_matches(body,pattern)
    matches_string = str(pp.pprint(matches))
    plugintools.log("movie4k.single_tvshow "+matches_string)
    
    for scrapedurl, hoster_name in matches:
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = hoster_name.strip()
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.single_tvshow title="+title+", url="+url+", thumbnail="+thumbnail)

        for hoster in HOSTERS_ALLOWED:
            #plugintools.log("<<<<<"+hoster+">>>>> IN <<<<<<"+title.lower()+">>>>>>")
            if hoster in title.lower():
                plugintools.add_item( action="play", title=LANG_PUBLISHED_AT+" "+title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )
                found = True

    if not found:
        play(params)
    

# Show movie links
def single_movie(params):
    pp = pprint.PrettyPrinter(indent=4)
    plugintools.log("movie4k.single_movie "+repr(params))

    #plugintools.set_view(plugintools.LIST)
    found = False

    body,response_headers = read_body_and_headers(params.get("url"))
    body = body.replace("\\\"","\"")

    '''
    <tr id="tablemoviesindex2">
    <td height="20" width="150">
    <a href="Thor-The-Dark-World-watch-movie-4650640.html">11/16/2013 
    <img border=0 style="vertical-align:top;" src="http://img.movie4k.to/img/divx.gif" alt="Divxstage Thor The Dark World" title="Divxstage Thor The Dark World" width="16"> &nbsp;Divxstage</a>
    </td><td align="right" width="58"><a href="Thor-The-Dark-World-watch-movie-4650640.html">Quality:</a> 
    <img style="vertical-align: top;" src="http://img.movie4k.to/img/smileys/1.gif" alt="Movie quality CAM Mic dubbed" title="Movie quality CAM Mic dubbed"></td></tr>
    '''

    '''
    links[4658139]="<tr id=\"tablemoviesindex2\"><td height=\"20\" width=\"150\"><a href=\"Thor-The-Dark-World-watch-movie-4658139.html\">11/17/2013 <img border=0 style=\"vertical-align:top;\" src=\"http://img.movie4k.to/img/flashPlayer2.gif\" alt=\"Sharesix Thor The Dark World\" title=\"Sharesix Thor The Dark World\" width=\"16\"> &nbsp;Sharesix</a></td><td align=\"right\" width=\"58\"><a href=\"Thor-The-Dark-World-watch-movie-4658139.html\">Quality:</a> <img style=\"vertical-align: top;\" src=\"http://img.movie4k.to/img/smileys/1.gif\" alt=\"Movie quality CAM Mic dubbed\" title=\"Movie quality CAM Mic dubbed\"></td></tr>";
    '''

    # comments
    pattern = '<td rowspan="2" width="[^"]+" class="comment" valign="top" bgcolor="[^"]+" style="padding-left:10px; padding-right:17px;">([^>]+)<div[^>]+'
    comments = plugintools.find_multiple_matches(body,pattern)
    # make comments as single text
    all_comments = "";
    for comment in comments:
        cleaned_comment = comment.strip()
        all_comments += cleaned_comment + "\n\n"
    
    # description
    pattern = '<div class="moviedescription">([^>]+)</div[^<]+'
    description = plugintools.find_single_match(body,pattern)
    description = description.strip()
    
    # movie details title
    '''
    <H1 style="font-size:18px;display:inline;">
        <a href="//Katakomben-online-film-5799311.html" style="color:#000000;">
            Katakomben        </a>
    </H1>    
    '''
    pattern  = '<H1 style="font-size:18px;display:inline;"[^<]+'
    pattern += '<a href="[^"]+" style="color:#000000;">([^>]+)</a[^<]+'
    details_title = plugintools.find_single_match(body,pattern)
    details_title = details_title.strip()
    trailer_title = LANG_SEARCH_TRAILERS_FOR_1 + " " + details_title + " " + LANG_SEARCH_TRAILERS_FOR_2
    details_title = LANG_DETAILS_TITLE_PREFIX +  " " + details_title
    
    # movie image 
    '''
    <img border="0" title="Katakomben" alt="Katakomben" style="width:105px;max-width:105px;max-height:160px;min-height:140px;" src="http://img.movie4k.tv/thumbs/cover-5645913-Katakomben-movie4k-film.jpg">    
    '''
    pattern  = '<img src="([^"]+)" border=0 style="[^"]+" alt="[^"]+" title="[^"]+"[^<]+'
    details_image = plugintools.find_single_match(body,pattern)
    details_image = details_image.strip()
    details_image = details_image.replace("img.movie4k.tv//","img.movie4k.tv/")
    # all over quality
    '''
    <span style="font-size:14px;">Quality: <img src="/img/smileys/5.gif" alt="Movie quality DVDRip/BDRip " title="Movie quality DVDRip/BDRip " style="vertical-align:top;">    
    '''
    pattern  = '<span style="font-size:14px;">Quality: <img src="([^"]+)" alt="[^"]+" title="[^"]+" style="vertical-align:top;"[^<]+'
    quality_gif = plugintools.find_single_match(body,pattern)
    quality_gif = quality_gif.strip()
    quality_gif = quality_gif.replace( "/img/smileys/", "" );
    quality = fetch_quality_by_gif(quality_gif)
    
    # movie language
    '''
    <img src="us_ger_small.png" width=24 height=14 border=0>
    '''
    pattern  = '<img src="([^"]+)" width=24 height=14 border=0>'
    flag = plugintools.find_single_match(body,pattern)
    flag = flag.strip()
    movie_language = get_language_from_flag_img(flag)
    movie_language = movie_language.replace("(","")
    movie_language = movie_language.replace(")","")
    movie_language = movie_language.strip()
    
    # genres of movie
    '''
    Genre: 
                            <a href="//movies-genre-14-Horror.html">Horror</a>
                ,          <a href="//movies-genre-23-Thriller.html">Thriller</a>
                &nbsp;|        
    '''
    pattern  = 'Genre:\ (.*?)\|'
    genre_content = plugintools.find_single_match(body,pattern)
    
    pattern  = '<a href="[^"]+">([^<]+)</a>'
    found_genres = plugintools.find_multiple_matches(genre_content, pattern)
    genre_string = ''
    roundtrip = 0
    for genre in found_genres:
        if roundtrip > 0:
            genre_string = genre_string + ", "
        genre_string = genre_string + genre.strip()
        roundtrip += 1
        
    # regie
    pattern  = 'Regie:\ (.*?)\|'
    regie_content = plugintools.find_single_match(body,pattern)
    
    pattern  = '<a href="[^"]+">([^<]+)</a>'
    found_regie = plugintools.find_multiple_matches(regie_content, pattern)
    regie_string = ''
    roundtrip = 0
    for regie in found_regie:
        if roundtrip > 0:
            regie_string = regie_string + ", "
        regie_string = regie_string + regie.strip()
        roundtrip += 1
        
        
    # actors
    pattern  = LANG_DETAILS_ACTORS + ':\ (.*?)\<BR>'
    actors_content = plugintools.find_single_match(body,pattern)
    
    pattern  = '<a href="[^"]+">([^<]+)</a>'
    found_actors = plugintools.find_multiple_matches(actors_content, pattern)
    actors_string = ''
    roundtrip = 0
    for actor in found_actors:
        actors_string = actors_string + "\n"
        actors_string = actors_string + actor.strip()
        
    actorsandmore = LANG_DETAILS_REGIE + "\n" + regie_string + "\n\n" + LANG_DETAILS_ACTORS + ":\n" + actors_string
    
    # land and year
    pattern  = LANG_DETAILS_LANDYEAR + ':\ (.*?)\<BR>'
    landyear_content = plugintools.find_single_match(body,pattern)
    landyear = landyear_content.strip()
    
    
   
    
    pattern  = '<tr id="tablemoviesindex2"[^<]+'
    pattern += '<td height="\d+" width="\d+"[^<]+'
    pattern += '<a href="([^"]+)">([^<]+)'
    pattern += '<img border=0 style="[^"]+" src="([^"]+)"[^>]+>([^<]+)</a[^<]+'
    pattern += '</td><td align="right" width="58"><a[^>]+>Quality.</a[^<]+'
    pattern += '<img style="[^"]+" src="([^"]+)" alt="[^"]+"'
    matches = plugintools.find_multiple_matches(body,pattern)
    
    # IMDB Id and rating
    '''
    IMDB Rating: <a href="http://www.imdb.com/title/tt816692" target="_blank">9.10</a>
    '''
    pattern = 'IMDB Rating: <a href="([^"]+)" target="_blank">([^>]+)</a>[^>]+'
    imdb_values = plugintools.find_multiple_matches(body,pattern)
    imdb_values = imdb_values[0]
    plugintools.log('movie4k.single_movie imdb values:' + str(pp.pprint(imdb_values)))
    plugintools.log('movie4k.single_movie imdb values length:' + str(len(imdb_values)))
    fsk_recommendation = ""
    if len(imdb_values) == 2:
        imdb_rating = imdb_values[1]
        imdb_link = imdb_values[0]
        splittet_imdb_link = imdb_link.split("/")
        imdb_id = splittet_imdb_link[-1]
        fsk_api_link = "http://altersfreigaben.de/api/imdb_id/" + imdb_id
        fsk_body,fsk_response_headers = read_body_and_headers(fsk_api_link)
        fsk_result = str(fsk_body.strip())
        
        if fsk_result == '100':
            fsk_recommendation = LANG_FSK_NOT_AVALABLE
        elif fsk_result == '200':
            fsk_recommendation = LANG_FSK_NOT_YET_RATED
        else:
            fsk_recommendation = LANG_FSK_FROM + " " + fsk_result + " " + LANG_FSK_YEARS
            
    plugintools.log("fsk_recommendation string: " +  fsk_recommendation)
    plugintools.log("imdb_rating: " + imdb_rating)
    # Details for film
    plugintools.add_item( action="movie_details", title=details_title, plot=description, comments=all_comments, thumbnail=details_image, imdb_rating=imdb_rating, actorsandmore=actorsandmore, quality=quality, landyear=landyear, genre=genre_string, movie_language=movie_language,age_recomm=fsk_recommendation, folder=False )
    
    # Add trailer search option
    plugintools.add_item( action="search_trailer", title=trailer_title, plot=description, comments=all_comments, thumbnail=details_image, imdb_rating=imdb_rating, actorsandmore=actorsandmore, quality=quality, landyear=landyear, genre=genre_string, movie_language=movie_language, folder=True )

    # Add actors of movie selection
    plugintools.add_item( action="show_actors_of_movie", url=params.get("url"), title=LANG_LIST_MAIN_ACTORS, plot=description, comments=all_comments, thumbnail=details_image, imdb_rating=imdb_rating, actorsandmore=actorsandmore, quality=quality, landyear=landyear, genre=genre_string, movie_language=movie_language, folder=True )

    for scrapedurl, date_added, server_thumbnail, server_name, quality in matches:
        quality_gif = quality.replace( "/img/smileys/", "" );
        quality = fetch_quality_by_gif(quality_gif)
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = server_name.strip().replace("&nbsp;","")+" (IMDB:"+imdb_rating+") ("+LANG_QUALITY+quality+") ("+date_added+")"
        thumbnail = ""
        plot = ""
        plugintools.log("movie4k.single_movie title="+title+", url="+url+", thumbnail="+thumbnail)

        for hoster in HOSTERS_ALLOWED:
            #plugintools.log("<<<<<"+hoster+">>>>> IN <<<<<<"+title.lower()+">>>>>>")
            if hoster in title.lower():
                plugintools.add_item( action="play", title=LANG_PUBLISHED_AT+" "+title, url=url, thumbnail=thumbnail , plot=plot, fanart=thumbnail , folder=True )
                found = True

    '''
    <tr id="tablemoviesindex2" onClick="window.location.href = 'tvshows-3415901-Arrow.html';return false;" style="cursor:hand;cursor:pointer;"><td style="padding-left:5px;height:20px;width:80px;"><a href="tvshows-3415901-Arrow.html">Episode 1</a>&nbsp;</td><td align="left" width="150"><a href="tvshows-3415901-Arrow.html" style="margin-left:18px;"><img border=0 style="vertical-align:top;" src="http://img.movie4k.to/img/divx.gif" alt="Filebox Arrow" title="Filebox Arrow" width="16"> &nbsp;Filebox</a></td></tr>
    '''
    '''
    links[1522783]="
    <tr id=\"tablemoviesindex2\" onClick=\"window.location.href = 'tvshows-1522783-Arrow.html';return false;\" style=\"cursor:hand;cursor:pointer;\">
    <td style=\"padding-left:5px;height:20px;width:80px;\">
    <a href=\"tvshows-1522783-Arrow.html\">Episode 1</a>&nbsp;
    </td>
    <td align=\"left\" width=\"150\">
    <a href=\"tvshows-1522783-Arrow.html\" style=\"margin-left:18px;\">
    <img border=0 style=\"vertical-align:top;\" src=\"http://img.movie4k.to/img/hoster/113.png\" alt=\"Sockshare Arrow\" title=\"Sockshare Arrow\" width=\"16\"> &nbsp;Sockshare</a></td></tr>";
    '''

    pattern  = '<tr id="tablemoviesindex2"[^<]+'
    pattern += '<td[^<]+'
    pattern += '<a href="([^"]+)"[^<]+</a[^<]+'
    pattern += '</td[^<]+'
    pattern += '<td[^<]+'
    pattern += '<a[^<]+<img border=0 style="[^"]+" src="([^"]+)"[^>]+>([^<]+)</a>'
    matches = plugintools.find_multiple_matches(body,pattern)

    for scrapedurl, scrapedthumbnail, scrapedtitle in matches:
        
        url = urlparse.urljoin(params.get("url"),scrapedurl)
        title = scrapedtitle.strip().replace("&nbsp;","")
        thumbnail = urlparse.urljoin(params.get("url"),scrapedthumbnail)
        thumbnail = thumbnail.replace("img.movie4k.tv//","img.movie4k.tv/")
        thumbnail = thumbnail.replace("https://","http://")
        plot = ""
        plugintools.log("movie4k.single_movie title="+title+", url="+url+", thumbnail="+thumbnail)

        for hoster in HOSTERS_ALLOWED:
            #plugintools.log("<<<<<"+hoster+">>>>> IN <<<<<<"+title.lower()+">>>>>>")
            if hoster in title.lower():
                plugintools.add_item( action="play", title=LANG_PUBLISHED_AT+" "+title, url=url, thumbnail=thumbnail , plot=plot, fanart=FANART , folder=True )
                found = True
                
                

    if not found:
        play(params)
        
# list actors of movie        
def show_actors_of_movie(params):
    pp = pprint.PrettyPrinter(indent=4)
    plugintools.log("movie4k.show_actors_of_movie "+repr(params))

    thumbnail = params.get("thumbnail")

    body,response_headers = read_body_and_headers(params.get("url"))
    body = body.replace("\\\"","\"")
    
    
    # actors
    pattern  = LANG_DETAILS_ACTORS + ':\ (.*?)\<BR>'
    actors_content = plugintools.find_single_match(body,pattern)
    
    pattern  = '<a href="([^"]+)">([^<]+)</a>'
    found_actors = plugintools.find_multiple_matches(actors_content, pattern)
    
    for actor_films_url, actor_name in found_actors:
        imdb_actor_image = get_imdb_actor_image(actor_name)
        plot=""
        plugintools.add_item( action="movies_all", title=actor_name, url=MAIN_URL+actor_films_url, thumbnail=imdb_actor_image , plot=plot, fanart=thumbnail , folder=True )

        
# search trailer for movie
def search_trailer(params):
    from urlresolver.types import HostedMediaFile
    '''
    http://gdata.youtube.com/feeds/api/videos/-/<MOVIETITLE>-trailer-<LANGUAGE>?max-results=10
    '''
    
    # trailer_base_url = "http://gdata.youtube.com/feeds/api/videos/-/"
    trailer_base_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=10&order=relevance&type=video&videoDefinition=high&key=" + YOUTUBE_API_KEY + "&q="
    youtube_baseurl = "http://www.youtube.com/watch?v="
    
    # create urlencoded title of film
    trailer_title = params.get("title")
    trailer_title = trailer_title.replace(LANG_SEARCH_TRAILERS_FOR_1,"")
    trailer_title = trailer_title.replace(LANG_SEARCH_TRAILERS_FOR_2,"")
    trailer_title = trailer_title.strip()
    # trailer_title = urllib.quote_plus(trailer_title)

    # create urlencoded language part
    if plugintools.get_setting('movie4k_language') == '1':
        trailer_language = " " + LANG_ENGLISH
    elif plugintools.get_setting('movie4k_language') == '2':  
        trailer_language = " " + LANG_GERMAN
    else:
        trailer_language = ""    
    
    search_string = trailer_title + " trailer" + trailer_language
    search_string = urllib.quote_plus(search_string)
    trailer_url = trailer_base_url + search_string
    plugintools.log("movie4k.search_trailer " + trailer_url)
    # call available trailers
    body,response_headers = simple_read_body_and_headers(trailer_url)
    
    videodata = json.loads(body)
    
    for trailer_video in videodata['items']:
        trailer_video_id = trailer_video['id']['videoId']
        trailer_video_title = trailer_video['snippet']['title']
        trailer_video_thumbnmail = trailer_video['snippet']['thumbnails']['high']['url']
        trailer_video_url = youtube_baseurl + trailer_video_id
        plot = ""
        
        hosted_media_file = HostedMediaFile(url=trailer_video_url)
        media_url = hosted_media_file.resolve()
        plugintools.log("movie4k.play hosted_media_file="+repr(hosted_media_file))
        plugintools.log("movie4k.play media_url="+repr(hosted_media_file))
        plugintools.add_item( action="playable", title=trailer_video_title, url=media_url, thumbnail=trailer_video_thumbnmail , plot=plot, fanart=trailer_video_thumbnmail, extra="istrailer", isPlayable=True, folder=False )
        
# play trailer    
def play_trailer(params):
    pp = pprint.PrettyPrinter(indent=4)
    params_string = str(pp.pprint(params))
    plugintools.log("movie4k.play_trailer params "+params_string)    
    
    from urlresolver.types import HostedMediaFile
    url=params.get("url")
    plugintools.log("movie4k.play_trailer url="+url)
    hosted_media_file = HostedMediaFile(url=url)
    media_url = hosted_media_file.resolve()
    plugintools.log("movie4k.play_trailer resolved media_url="+media_url)
    if media_url:
        plugintools.play_resolved_url(media_url)
    else:
        plugintools.add_item( action="play", title=LANG_VIDEO_NOT_PLAYABLE, isPlayable=True, folder=False )
    
        
#show details information
def movie_details(params):
    pp = pprint.PrettyPrinter(indent=4)
    params_string = str(pp.pprint(params))
    plugintools.log("movie4k.movie_details "+params_string)    
    detailsdisplay=MovieDetailsDialog()
    # add params
    detailsdisplay.setMovieTitle(params.get("title"))
    detailsdisplay.setMovieImage(params.get("thumbnail"))
    detailsdisplay.setMovieDescription(params.get("plot"))
    detailsdisplay.setMovieComments(params.get("comments"))
    detailsdisplay.setMovieActorsAndMore(params.get("actorsandmore"))
    detailsdisplay.setLanguage(params.get("movie_language"))
    detailsdisplay.setQuality(params.get("quality"))
    detailsdisplay.setImdb(params.get("imdb_rating"))
    detailsdisplay.setGenre(params.get("genre"))
    detailsdisplay.setLandYear(params.get("landyear"))
    detailsdisplay.setMovieAgeRecommendation(params.get("age_recomm"))
    # display details
    detailsdisplay.doModal()
    

# Resolve hoster links
def play(params):
    plugintools.log("movie4k.play "+repr(params))
    extraparam = params.get("extra")

    #plugintools.set_view(plugintools.LIST)

    try:
        body,response_headers = read_body_and_headers(params.get("url"))
        redirect_url = ''
        for key, value in response_headers:
            if key == 'location' and value != '':
                redirect_url = value
        
        if redirect_url != '' and extraparam != "istrailer":
            body,response_headers = read_body_and_headers(MAIN_URL+redirect_url)
        
        plugintools.log("movie4k.play body="+repr(body))

        url = plugintools.find_single_match(body,'<a target="_blank" href="([^"]+)">')
        plugintools.log("movie4k.play url="+repr(url))

        if url=="":
            url = plugintools.find_single_match(body,'<iframe.*?src="([^"]+)"')

        if url!="":
            if url.startswith("http://www.nowvideo.sx/video/"):
                url = url.replace("http://www.nowvideo.sx/video/","http://embed.nowvideo.eu/embed.php?v=")+"&width=600&height=480"

            from urlresolver.types import HostedMediaFile
            hosted_media_file = HostedMediaFile(url=url)
            plugintools.log("movie4k.play hosted_media_file="+repr(hosted_media_file))

            try:
                media_url = hosted_media_file.resolve()
                plugintools.log("movie4k.play media_url="+repr(media_url))

                if media_url:
                    plugintools.add_item( action="playable", title=LANG_PLAY_VIDEO_FROM+" [B]"+hosted_media_file.get_host()+"[/B] ["+get_filename_from_url(media_url)[-4:]+"]", url=media_url, isPlayable=True, folder=False )
                else:
                    plugintools.add_item( action="play", title=LANG_VIDEO_NOT_PLAYABLE, isPlayable=True, folder=False )
            except:
                plugintools.add_item( action="play", title=LANG_HOSTER_DOWN, isPlayable=True, folder=False )
                plugintools.add_item( action="play", title=LANG_SELECT_OTHER_OPTION, isPlayable=True, folder=False )

        else:
            plugintools.add_item( action="play", title=LANG_NO_VALID_LINKS_FOUND, isPlayable=True, folder=False )

    except urllib2.URLError,e:
        plugintools.add_item( action="play", title=LANG_ERROR_READING_DATA_M4K, isPlayable=True, folder=False )
        body = ""

    if params.get("extra")=="noalternatives":
        plugintools.log("movie4k.play noalternatives")
    else:
        #<OPTION value="Arrow-watch-tvshow-1522775.html" style="Background: URL('http://img.movie4k.to/img/hoster/186.png') no-repeat 3px center transparent; Text-Indent: 25px">Nowvideo (2/7)</OPTION>
        bloque = plugintools.find_single_match(body,'<SELECT name="hosterlist(.*?)</SELECT')
        pattern  = '<OPTION value="([^"]+)"[^>]+>([^<]+)</OPTION>'
        matches = plugintools.find_multiple_matches(bloque,pattern)

        for scrapedurl, scrapedtitle in matches:
            
            url = urlparse.urljoin(params.get("url"),scrapedurl)
            title = scrapedtitle.strip()
            thumbnail = ""
            plot = ""
            plugintools.log("movie4k.play title="+title+", url="+url+", thumbnail="+thumbnail)

            for hoster in HOSTERS_ALLOWED:
                #plugintools.log("<<<<<"+hoster+">>>>> IN <<<<<<"+title.lower()+">>>>>>")
                if hoster in title.lower():
                    plugintools.add_item( action="play", title=LANG_ALTENATIVE_LINK_FOUND+" "+title, url=url, thumbnail=thumbnail , plot=plot, fanart=FANART , folder=True, extra="noalternatives" )

# Play hoster link
def playable(params):
    plugintools.play_resolved_url( params.get("url") )    

def get_filename_from_url(url):
    
    parsed_url = urlparse.urlparse(url)
    try:
        filename = parsed_url.path
    except:
        if len(parsed_url)>=4:
            filename = parsed_url[2]
        else:
            filename = ""

    return filename

def get_language_from_flag_img(url):
    if "us_flag" in url:
        return " ("+LANG_ENGLISH+")"
    elif "us_ger" in url:
        return " ("+LANG_GERMAN+")"
    elif "flag_spain" in url:
        return " ("+LANG_SPANISH+")"
    elif "flag_greece" in url:
        return " ("+LANG_GREEK+")"
    elif "flag_turkey" in url:
        return " ("+LANG_TURK+")"
    elif "flag_russia" in url:
        return " ("+LANG_RUSSIAN+")"
    elif "flag_japan" in url:
        return " ("+LANG_JAPANESE+")"
    elif "flag_france" in url:
        return " ("+LANG_FRENCH+")"

    return ""

def read_body_and_headers(url, post=None, headers=[], follow_redirects=False, timeout=None):
    plugintools.log("movie4k.read_body_and_headers url="+url)

    expiration = datetime.datetime.now() + datetime.timedelta(days=365)
    expiration_gmt = expiration.strftime("%a, %d-%b-%Y %H:%M:%S PST")

    if plugintools.get_setting("movie4k_language")=="1":
        plugintools.log("movie4k.read_body_and_headers only english")
        headers.append(["Cookie","onlylanguage=en; expires="+expiration_gmt+"; xxx2=ok; expires="+expiration_gmt+";"])
    elif plugintools.get_setting("movie4k_language")=="2":
        plugintools.log("movie4k.read_body_and_headers only german")
        headers.append(["Cookie","onlylanguage=de; expires="+expiration_gmt+"; xxx2=ok; expires="+expiration_gmt+";"])
    else:
        headers.append(["Cookie","xxx2=ok; expires="+expiration_gmt+";"])

    try:
        body,response_headers = plugintools.read_body_and_headers(url,post,headers,follow_redirects,timeout)
    except:
        xbmc.sleep(3)
        body,response_headers = plugintools.read_body_and_headers(url,post,headers,follow_redirects,timeout)

    return body,response_headers


def simple_read_body_and_headers(url, post=None, headers=[], follow_redirects=False, timeout=None):
    plugintools.log("movie4k.simple_read_body_and_headers url="+url)
    try:
        body,response_headers = plugintools.simple_read_body_and_headers(url,post,headers,follow_redirects,timeout)
    except:
        xbmc.sleep(3)
        body,response_headers = plugintools.simple_read_body_and_headers(url,post,headers,follow_redirects,timeout)

    return body,response_headers
    
    

def fetch_quality_by_gif(quality_gif):
    quality_string = LANG_QUALITY_0
    if quality_gif == "1.gif":
        quality_string = LANG_QUALITY_1
    elif quality_gif == "2.gif":
        quality_string = LANG_QUALITY_2
    elif quality_gif == "3.gif":
        quality_string = LANG_QUALITY_3
    elif quality_gif == "4.gif":
        quality_string = LANG_QUALITY_4
    elif quality_gif == "5.gif":
        quality_string = LANG_QUALITY_5
        
    return quality_string

def get_imdb_actor_image(actor_name):
    plugintools.log("movie4k.get_imdb_actor_image for name "+actor_name)
    actor_name_urlencoded = urllib.quote_plus(actor_name)
    imdb_actor_id_url = 'http://www.imdb.com/xml/find?json=1&nr=1&nm=on&q=' + actor_name_urlencoded
    
    body,response_headers = read_body_and_headers(imdb_actor_id_url)
    body = body.replace("\\\"","\"")
    
    pattern = '"name_popular": \[\{ "id":"([^"]+)", "title"[^"]+'
    imdb_actor_id = plugintools.find_single_match(body,pattern)
    
    imdb_actor_picture_url = ''
    if imdb_actor_id != "":
        imdb_actor_page_url = 'http://www.imdb.com/name/' + imdb_actor_id + '/'
        body,response_headers = read_body_and_headers(imdb_actor_page_url)
        body = body.replace("\\\"","\"")

        pattern = '<img id="name-poster"\nheight="317"\nwidth="214"\nalt="[^"]+"\ntitle="[^"]+"\nsrc="([^"]+)"\nitemprop="image" \/>[^>]+'
        imdb_actor_picture_url = plugintools.find_single_match(body,pattern)

    
    return imdb_actor_picture_url
    
run()
