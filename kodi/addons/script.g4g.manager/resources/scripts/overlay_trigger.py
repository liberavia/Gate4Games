#!/usr/bin/env python
import os
import sys
import signal
import subprocess
import stat
import thread
import threading
import time
import shutil
import json
from Xlib.display import Display
from Xlib import X
from os.path import expanduser

HOME_DIR = expanduser("~")
FOLDER_G4G = os.path.join(HOME_DIR, '.g4g')
FOLDER_TEMP = os.path.join(FOLDER_G4G, 'temp')
FOLDER_GAME_MANAGER_SCRIPTS = os.path.join(HOME_DIR, '.kodi', 'addons', 'script.g4g.manager', 'resources', 'scripts')
OVERLAY_RUNS_PATH = os.path.join(FOLDER_TEMP, 'overlay.pid')
OVERLAY_GAME_RUNNING = os.path.join(FOLDER_TEMP, 'gameinfo')
PIDFILE_GAME_RUNNING = os.path.join(FOLDER_TEMP, 'game.pid')


def handle_event(aEvent,disp):
    try:
        keycode = aEvent.detail
        if keycode == 76:
            time.sleep(0.5)
            triggerOverlay(disp)
    except:
        pass
        
def getGameInfos():
    if os.path.exists(OVERLAY_GAME_RUNNING) and os.path.exists(PIDFILE_GAME_RUNNING):
        gameinfo_file = open(OVERLAY_GAME_RUNNING,'r')
        pidfile_game = open(PIDFILE_GAME_RUNNING,'r')
        game_info = json.load(gameinfo_file)
        with pidfile_game as f:
            pid_running_game = f.readline()
        gameinfos = {"gameName" : game_info['gamename'], "gameControllerConfig": game_info['controllerconfig']}
        gameinfo_file.close()
        pidfile_game.close()
    else:
        gameinfos = {"gameName" : "No game running", "gameControllerConfig": "Playstation1"}
        
    return gameinfos
        

def triggerOverlay(disp):
    if os.path.exists(OVERLAY_RUNS_PATH):
        print "is running => close it now"
        pidfile = open(OVERLAY_RUNS_PATH, 'r')
        with pidfile as f:
            pid_to_kill = f.readline()
        if pid_to_kill != None:
            # restore controller config
            gameinfos = getGameInfos()
            subprocess.Popen('killall -9 qjoypad', shell=True, close_fds=True)
            time.sleep(1)
            cmd_game_layout = 'qjoypad ' + gameinfos['gameControllerConfig']
            print "QJOYPAD COMMAND GAME: " + cmd_game_layout
            subprocess.Popen(cmd_game_layout, shell=True, close_fds=True)
            # kill process
            print "KILLING OVERLAY PID: " + pid_to_kill.strip()
            #subprocess.Popen('kill -9 ' + pid_to_kill.strip(), shell=True, close_fds=True)
            os.kill(int(pid_to_kill.strip()), signal.SIGTERM) #or signal.SIGKILL 
            pidfile.close()
            os.remove(OVERLAY_RUNS_PATH)
    else:
        print "is not running => open it now"
        subprocess.Popen('killall -9 qjoypad', shell=True, close_fds=True)
        time.sleep(1)
        cmd_overlay_layout = 'qjoypad g4goverlay'
        print "QJOYPAD COMMAND OVERLAY: " + cmd_overlay_layout
        subprocess.Popen(cmd_overlay_layout, shell=True, close_fds=True)
        
        #execute_path = "/home/kbox/Entwicklung/Spielplatz/Python/g4goverlay.py"
        execute_path = os.path.join(FOLDER_GAME_MANAGER_SCRIPTS, 'g4goverlay.py')
        st = os.stat(execute_path)
        os.chmod(execute_path, st.st_mode | stat.S_IEXEC)
        process = subprocess.Popen(execute_path, shell=False, close_fds=True)
        # write pid into file
        print "SAVING OVERLAY PID: " + str(process.pid)
        pidfile = open(OVERLAY_RUNS_PATH, 'w')
        pidfile.write(str(process.pid))
        pidfile.close()
    # close display restart main routine or we gonna end up in an endless loop    
    disp.close()
    main()

def main():
    # current display
    disp = Display()
    root = disp.screen().root

    # we tell the X server we want to catch keyPress event
    root.change_attributes(event_mask = X.KeyPressMask)

    # get superkey
    root.grab_key(76, X.AnyModifier, 1,X.GrabModeAsync, X.GrabModeAsync)

    while 1:
        event = root.display.next_event()
        handle_event(event,disp)

if __name__ == '__main__':
    if os.path.exists(OVERLAY_RUNS_PATH):
        os.remove(OVERLAY_RUNS_PATH)
    main()