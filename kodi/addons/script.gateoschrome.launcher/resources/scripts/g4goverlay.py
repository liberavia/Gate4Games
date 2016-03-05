#!/usr/bin/python3
# -*- coding: utf-8 -*-

import os
import sys
import subprocess
import signal
import time
import shutil
import stat
import json
from os.path import expanduser
from PyQt5.QtGui import *
from PyQt5.QtWidgets import *
#from PyQt5.QtWidgets import QWidget, QPushButton, QHBoxLayout, QVBoxLayout, QApplication
from PyQt5.QtCore import *


HOME_DIR = expanduser("~")
FOLDER_G4G = os.path.join(HOME_DIR, '.g4g')
FOLDER_TEMP = os.path.join(FOLDER_G4G, 'temp')
OVERLAY_GAME_RUNNING = os.path.join(FOLDER_TEMP, 'gameinfo')
PIDFILE_GAME_RUNNING = os.path.join(FOLDER_TEMP, 'game.pid')
FOLDER_GAME_MANAGER_SCRIPTS = os.path.join(HOME_DIR, '.kodi', 'addons', 'script.g4g.manager', 'resources', 'scripts')
OVERLAY_CSS = os.path.join(FOLDER_GAME_MANAGER_SCRIPTS, 'overlay.css')

class g4gOverlayWindow(QWidget):
    
    def __init__(self):
        super().__init__()
        self.setGameInfo()
        self.initUI()
        
    def getMainColor(self):
        return QColor(93,0,93)
    
    def getHeadlineFont(self):
        font = QFont('FreeMono', 26)
        font.setBold(True)
        
        return font
        
    def paintEvent(self,event=None):
        backgroundColor = Qt.black
        painter = QPainter(self)
        painter.setOpacity(0.8)
        painter.setBrush(backgroundColor)
        painter.setPen(QPen(backgroundColor))
        painter.drawRect(self.rect())
        
    def setGameInfo(self):
        if os.path.exists(OVERLAY_GAME_RUNNING) and os.path.exists(PIDFILE_GAME_RUNNING):
            gameinfo_file = open(OVERLAY_GAME_RUNNING,'r')
            pidfile_game = open(PIDFILE_GAME_RUNNING,'r')
            game_info = json.load(gameinfo_file)
            with pidfile_game as f:
                pid_running_game = f.readline()
            self.gamePid = int(pid_running_game.strip())
            self.gameName = game_info['gamename']
            self.gameControllerConfig = game_info['controllerconfig']
            gameinfo_file.close()
            pidfile_game.close()
        else:
            self.gamePid = None
            self.gameName = "No game"
            self.gameControllerConfig = "Playstation1"

        
    def initUI(self):      
        # placing elements
        palette = QPalette()
        palette.setColor(QPalette.Foreground,Qt.white)
        
        LabelTitle = QLabel(self.gameName)
        LabelTitle.setContentsMargins(0,0,0,75)
        
        ButtonStopGame = QPushButton("Quit Game")
        ButtonStopGame.setFixedWidth(450)
        ButtonConfigPad = QPushButton("Configure Controller")
        ButtonConfigPad.setFixedWidth(450)
        
        hboxLayoutTitle = QHBoxLayout()
        hboxLayoutTitle.addStretch(1)
        hboxLayoutTitle.addWidget(LabelTitle)
        hboxLayoutTitle.addStretch(1)
        
        vboxButtons = QVBoxLayout()
        vboxButtons.addWidget(ButtonStopGame)
        vboxButtons.addWidget(ButtonConfigPad)
        vboxButtons.addStretch(1)

        hboxMainArea = QHBoxLayout()
        hboxMainArea.addLayout(vboxButtons)
        hboxMainArea.addStretch(1)
        
        overlayLayout = QVBoxLayout()
        #overlayLayout.setContentsMargins(0,0,0,100)
        overlayLayout.addLayout(hboxLayoutTitle)
        overlayLayout.addLayout(hboxMainArea)

        self.setLayout(overlayLayout)    
        ButtonStopGame.setFocus()

        # general settings like putting overlay in front and beeing frameless and semi transparent
        self.setWindowFlags( Qt.WindowStaysOnTopHint | Qt.FramelessWindowHint ) #Qt.X11BypassWindowManagerHint | 
        self.setAttribute(Qt.WA_NoSystemBackground)
        self.setAttribute(Qt.WA_TranslucentBackground)
        #self.showMaximized()
        self.showFullScreen()
        # Signaling
        ButtonStopGame.clicked.connect(self.quitGame)
        
        # show what we've got!
        self.show()
            
    #def keyPressEvent(self, e):
        #print(e.key())
        ## meta key pressed => bye bye
        #if e.key() == 16777264:
            #self.close()
            
        # 16777235 => UP
        # 16777234 => LEFT
        # 16777237 => DOWN
        # 16777236 => RIGHT
        
    def quitGame(self):
        quit_msg = "Are you sure you want to quit " + self.gameName + "?"
        reply = QMessageBox.question(self, 'Message', quit_msg, QMessageBox.Yes, QMessageBox.No )
        
        if reply == QMessageBox.Yes:
            if self.gamePid != None:
                print(str(self.gamePid))
                # os.kill(self.gamePid, signal.SIGTERM) # => Does nto work, don't know why
                subprocess.Popen("kill -9 " + str(self.gamePid), shell=True, close_fds=True)
                os.remove(OVERLAY_GAME_RUNNING)
                os.remove(PIDFILE_GAME_RUNNING)
                self.close()
                
            

def main():
    app = QApplication(sys.argv)
    with open(OVERLAY_CSS,"r") as fh:
        app.setStyleSheet(fh.read())    
    g4goverlay = g4gOverlayWindow()
    sys.exit(app.exec_()) 

if __name__ == '__main__':
    main() 

