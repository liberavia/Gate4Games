
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
QR_CODE_IMAGE = 9901

# Plugin Info
ADDON_ID = 'script.g4g.manager'
REAL_SETTINGS = xbmcaddon.Addon(id=ADDON_ID)
ADDON_ID = REAL_SETTINGS.getAddonInfo('id')
ADDON_NAME = REAL_SETTINGS.getAddonInfo('name')
ADDON_PATH = REAL_SETTINGS.getAddonInfo('path')
ADDON_VERSION = REAL_SETTINGS.getAddonInfo('version')
xbmc.log(ADDON_ID +' '+ ADDON_NAME +' '+ ADDON_PATH +' '+ ADDON_VERSION)
SkinMasterPath = os.path.join(ADDON_PATH, 'skins' ) + '/'
MySkinPath = (os.path.join(SkinMasterPath, '720p')) + '/'
MySkin = 'ShowQrCode.xmll'

class ShowQrCodeDialog(xbmcgui.WindowXMLDialog):
    def __new__(cls):
        return super(ShowQrCodeDialog, cls).__new__(cls, "ShowQrCode.xml", ADDON_PATH)

    def __init__(self):
        super(ShowQrCodeDialog, self).__init__()
        
    def onInit(self):
      self.getControl(QR_CODE_IMAGE).setImage(self.gameQrCodeUrl)
      pass
        
    def setGameQrCode(self, MyImageUrl):
        self.gameQrCodeUrl = MyImageUrl

'''
xbmc.log("MySkin is: " + MySkin)
mydisplay=MyFirstWinXML()
mydisplay.mySetLabelText("Text is now differen[CR]The quick brown fox jumps over the lazy dog![CR]The quick brown fox jumps over the lazy dog!")
mydisplay.doModal()
'''
 
