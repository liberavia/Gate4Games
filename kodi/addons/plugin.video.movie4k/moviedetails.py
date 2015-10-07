
import xbmc
import xbmcplugin
import xbmcaddon
import xbmcgui

import os
import sys
import datetime
import urlparse
import urllib2

#get actioncodes from https://github.com/xbmc/xbmc/blob/master/xbmc/guilib/Key.h
ACTION_PREVIOUS_MENU = 10
ACTION_SELEC_ITEM = 7
# Element IDs
MOVIE_TITLE = 9900
MOVIE_IMAGE = 9901
MOVIE_QUALITY = 9902
MOVIE_LANGUAGE = 9903
MOVIE_LANDYEAR = 9904
MOVIE_IMDB = 9905
MOVIE_GENRE = 9906
MOVIE_ACTORS = 9907
MOVIE_DESCRIPTION = 9908
MOVIE_COMMENTS = 9909
MOVIE_AGE_RECOMMENDATION = 9910

# Plugin Info
ADDON_ID = 'plugin.video.movie4k'
REAL_SETTINGS = xbmcaddon.Addon(id=ADDON_ID)
ADDON_ID = REAL_SETTINGS.getAddonInfo('id')
ADDON_NAME = REAL_SETTINGS.getAddonInfo('name')
ADDON_PATH = REAL_SETTINGS.getAddonInfo('path')
ADDON_VERSION = REAL_SETTINGS.getAddonInfo('version')
xbmc.log(ADDON_ID +' '+ ADDON_NAME +' '+ ADDON_PATH +' '+ ADDON_VERSION)
SkinMasterPath = os.path.join(ADDON_PATH, 'skins' ) + '/'
MySkinPath = (os.path.join(SkinMasterPath, '720p')) + '/'
MySkin = 'MovieDetails.xml'

class MovieDetailsDialog(xbmcgui.WindowXMLDialog):
    def __new__(cls):
        return super(MovieDetailsDialog, cls).__new__(cls, "MovieDetails.xml", ADDON_PATH)

    def __init__(self):
        super(MovieDetailsDialog, self).__init__()
        
    def onInit(self):
      self.getControl(MOVIE_TITLE).setLabel(self.movieTitle)
      self.getControl(MOVIE_IMAGE).setImage(self.movieImageUrl)
      self.getControl(MOVIE_DESCRIPTION).setText(self.movieDescription)
      self.getControl(MOVIE_COMMENTS).setText(self.movieComments)
      self.getControl(MOVIE_ACTORS).setText(self.movieActorsAndMore)
      self.getControl(MOVIE_LANGUAGE).setText(self.movieLanguage)
      self.getControl(MOVIE_QUALITY).setText(self.movieQuality)
      self.getControl(MOVIE_IMDB).setText(self.movieImdb)
      self.getControl(MOVIE_GENRE).setText(self.movieGenre)
      self.getControl(MOVIE_LANDYEAR).setText(self.movieLandYear)
      self.getControl(MOVIE_AGE_RECOMMENDATION).setText(self.movieAgeRecommendation)
      pass
        
    def setMovieDescription(self, MyText):
        self.movieDescription = MyText

    def setMovieTitle(self, MyText):
        self.movieTitle = MyText

    def setMovieComments(self, MyText):
        self.movieComments = MyText

    def setMovieImage(self, MyImageUrl):
        self.movieImageUrl = MyImageUrl
        
    def setMovieActorsAndMore(self, MyActorsAndMore):
        self.movieActorsAndMore = MyActorsAndMore
        
    def setQuality(self, MyQuality):
        self.movieQuality = MyQuality
        
    def setLanguage(self, MyLanguage):
        self.movieLanguage = MyLanguage
        
    def setImdb(self, MyImdb):
        self.movieImdb = MyImdb
        
    def setGenre(self, MyGenre):
        self.movieGenre = MyGenre
        
    def setLandYear(self, MyLandYear):
        self.movieLandYear = MyLandYear

    def setMovieAgeRecommendation(self, MyMovieAgeRecommendation):
        self.movieAgeRecommendation = MyMovieAgeRecommendation

'''
xbmc.log("MySkin is: " + MySkin)
mydisplay=MyFirstWinXML()
mydisplay.mySetLabelText("Text is now differen[CR]The quick brown fox jumps over the lazy dog![CR]The quick brown fox jumps over the lazy dog!")
mydisplay.doModal()
'''
 
