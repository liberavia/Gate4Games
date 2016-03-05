#simple chrome launcher for gateOS by liberavia
import os
import sys
import subprocess
import time
import shutil
import stat
import xbmc
import xbmcaddon
import xbmcgui

addon = xbmcaddon.Addon(id='script.gateoschrome.launcher')
addonPath = addon.getAddonInfo('path')
addonIcon = addon.getAddonInfo('icon')
addonVersion = addon.getAddonInfo('version')
dialog = xbmcgui.Dialog()
language = addon.getLocalizedString
scriptid = 'script.gateoschrome.launcher'

#HACK: sys.getfilesystemencoding() is not supported on all systems (e.g. Android)
txt_encode = 'utf-8'
try:
	txt_encode = sys.getfilesystemencoding()
except:
	pass

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


def makeScriptExec():
    scriptPath = os.path.join(getAddonInstallPath(), 'resources', 'scripts', 'chrome-launch.sh')
    if os.path.isfile(scriptPath):
        if not stat.S_IXUSR & os.stat(scriptPath)[stat.ST_MODE]:
            log('chrome-launch.sh not executable: %s' % scriptPath)
            try:
                os.chmod(scriptPath, stat.S_IRWXU)
                log('chrome-launch.sh now executable: %s' % scriptPath)
            except:
                log('ERROR: unable to make chrome-launch.sh executable, exiting: %s' % scriptPath)
                dialog.notification(language(50123), language(50126), addonIcon, 5000)
                sys.exit()
            log('chrome-launch.sh executable: %s' % scriptPath)


def kodiBusyDialog():
    xbmc.executebuiltin("ActivateWindow(busydialog)")
    log('busy dialog started')
    time.sleep(10)
    xbmc.executebuiltin("Dialog.Close(busydialog)")


def launchChrome():
    basePath = os.path.join(getAddonInstallPath(), 'resources', 'scripts')
    chromelauncher = os.path.join(basePath, 'chrome-launch.sh')
    cmd = '"%s"' % (chromelauncher)
    try:
        log('attempting to launch: %s' % cmd)
        print cmd.encode('utf-8')
        subprocess.Popen(cmd.encode(txt_encode), shell=True, close_fds=True)
        kodiBusyDialog()
    except:
        log('ERROR: failed to launch: %s' % cmd)
        print cmd.encode(txt_encode)
        dialog.notification(language(50123), language(50126), addonIcon, 5000)


log('****Running Chrome-Launcher v%s....' % addonVersion)
log('foreseen ONLY to run on gateOS')
log('System text encoding in use: %s' % txt_encode)

makeScriptExec()
launchChrome()