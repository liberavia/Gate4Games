import xbmc
import xbmcplugin
import xbmcaddon
import xbmcgui
import subprocess
import os
import sys
import datetime
import urlparse
import urllib2
import ntpath
import re
import json
from os.path import expanduser


#get actioncodes from https://github.com/xbmc/xbmc/blob/master/xbmc/guilib/Key.h
ACTION_PREVIOUS_MENU = 10
ACTION_SELEC_ITEM = 7
# Element IDs
LABEL_HEADLINE = 9900
LABEL_PERCENT = 9901
LABEL_DOWNLOADED = 9902
LABEL_REMAINING = 9903
LABEL_MESSAGE = 9908
LABEL_RATE = 9909
IMAGE_TITLE = 9904
PROGRESSBAR = 9905
BUTTON_ABORT = 9907

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
MySkin = 'ShowDownload.xml'

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


class ShowDownloadDialog(xbmcgui.WindowXMLDialog):
    def __new__(cls):
        return super(ShowDownloadDialog, cls).__new__(cls, "ShowDownload.xml", ADDON_PATH)

    def __init__(self):
        super(ShowDownloadDialog, self).__init__()
        
    def onInit(self):
        self.getControl(LABEL_PERCENT).setLabel(str(self.downloadPercent) + " %")
        self.getControl(IMAGE_TITLE).setImage(self.downloadImageUrl)
        self.getControl(LABEL_HEADLINE).setLabel(self.downloadTitle)
        self.getControl(PROGRESSBAR).setPercent(float(self.downloadPercent))
        self.getControl(LABEL_MESSAGE).setLabel(str(self.downloadMessage) + "...")
        self.getControl(LABEL_REMAINING).setLabel(str(self.downloadRemainingTime))
        self.getControl(LABEL_RATE).setLabel(str(self.downloadCurrentRate))
        pass
        
    def onClick(self, controlID):
        xbmc.log('Triggered clikck with ID' + str(controlID))
        if controlID == BUTTON_ABORT:
            xbmc.log('Triggered ABORT DOWNLOAD')
            # user triggered abort
            self.abortDownload()

    def abortDownload(self):
        ProgressId              = self.downloadProgressId
        xbmc.log('ProgressID is ' + ProgressId)
        script_filepath         = FOLDER_SCRIPTS + "/script_" + ProgressId
        desktop_filepath        = FOLDER_APPS + "/game_" + ProgressId + ".desktop"
        icon_filepath           = FOLDER_ICONS + "/icon_" + ProgressId + ".jpg"
        cover_filepath          = FOLDER_COVER + "/cover_" + ProgressId + ".jpg"
        fanart_filepath         = FOLDER_FANART + "/fanart_" + ProgressId + ".jpg"
        progress_filepath       = FOLDER_PROGRESS + "/progress_" + ProgressId + ".json"
        xbmc.log('Progress Filepath is ' + progress_filepath)
        download_filepath_zip   = FOLDER_DOWNLOADS + "/download_" + ProgressId + ".zip"
        download_filepath_targz = FOLDER_DOWNLOADS + "/download_" + ProgressId + ".tar.gz"
        download_filepath_7z    = FOLDER_DOWNLOADS + "/download_" + ProgressId + ".7z"
        
        # read file and get process id
        process_id = False
        progress_file = open(progress_filepath,'r')
        with progress_file as json_file:
            progress_info = json.load(json_file)
            process_id = progress_info['pid']
            xbmc.log('PROCESS_ID is ' + process_id)
        progress_file.close()
        
        # kill process
        if process_id != False:
            cmd = 'kill -9 ' + process_id
            xbmc.log('KILL Process with: ' + cmd)
            #install_process = subprocess.Popen(cmd, shell=True, close_fds=True)
            subprocess.call(["kill", "-9", process_id])
            #install_process.communicate()
            
        # check existing files and clean up
        if os.path.isfile(script_filepath):
            os.remove(script_filepath)
        if os.path.isfile(desktop_filepath):
            os.remove(desktop_filepath)
        if os.path.isfile(icon_filepath):
            os.remove(icon_filepath)
        if os.path.isfile(cover_filepath):
            os.remove(cover_filepath)
        if os.path.isfile(fanart_filepath):
            os.remove(fanart_filepath)
        if os.path.isfile(progress_filepath):
            xbmc.log('Removing ' + progress_filepath)
            os.remove(progress_filepath)
            
        for downloadpath in os.listdir(FOLDER_DOWNLOADS):
            downloadfile = ntpath.basename(downloadpath)
            xbmc.log('Check DownloadFile: ' + downloadfile +  " in  DownloadPath: " + downloadpath)
            if downloadfile.startswith("download_" + ProgressId):
                deletepath = os.path.join(FOLDER_DOWNLOADS, downloadfile)
                os.remove(deletepath)
            
        xbmc.executebuiltin('Container.Update')
        xbmc.executebuiltin('Container.Refresh')
        
    def setProgressId(self, ProgressId):
        self.downloadProgressId = ProgressId
        

    def setProgressFilePath(self, FilePath):
        self.downloadFilePath = FilePath
        app_filename = ntpath.basename(FilePath)
        app_install_id_list = re.findall(r'\d+',app_filename)
        app_install_id = str( app_install_id_list[0] )
        self.setProgressId(app_install_id)


    def setDownloadPercent(self, Percent):
        self.downloadPercent = Percent
        
    def setDownloadTitle(self, Title):
        self.downloadTitle = Title

    def setDownloadImage(self, ImageUrl):
        self.downloadImageUrl = ImageUrl

    def setDownloadDownloaded(self, Downloaded):
        self.downloadDownloaded = Downloaded

    def setDownloadRemainingTime(self, RemainingTime):
        self.downloadRemainingTime = RemainingTime

    def setDownloadCurrentRate(self, CurrentRate):
        self.downloadCurrentRate = CurrentRate

    def setDownloadMessage(self, Message):
        self.downloadMessage = Message

    def updateDownloadPercent(self, Percent):
        self.getControl(LABEL_PERCENT).setLabel(str(Percent) + " %")
        self.getControl(PROGRESSBAR).setPercent(float(Percent))
        pass

    def updateDownloadMessage(self, Message):
        self.getControl(LABEL_MESSAGE).setLabel(str(Message) + "...")
        pass
    
    def updateDownloadRemainingTime(self, RemainingTime):
        xbmc.log('Update RemainingTime to : ' + RemainingTime)
        self.getControl(LABEL_REMAINING).setLabel(str(RemainingTime) + "")
        pass
    
    def updateDownloadCurrentRate(self, CurrentRate):
        xbmc.log('Update CurrentRate to : ' + str(CurrentRate))
        self.getControl(LABEL_RATE).setLabel(str(CurrentRate) + "")
        pass

'''
xbmc.log("MySkin is: " + MySkin)
mydisplay=MyFirstWinXML()
mydisplay.mySetLabelText("Text is now differen[CR]The quick brown fox jumps over the lazy dog![CR]The quick brown fox jumps over the lazy dog!")
mydisplay.doModal()
'''
 
