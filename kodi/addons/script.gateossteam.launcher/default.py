# steam launcher for gateOS by liberavia based on teeedubb Steam Launcher. http://forum.xbmc.org/showthread.php?tid=157499
# And I used teedubb's addon as a template. Thanks a lot for your work, so I can go on doing other amazing stuff for gateOS :-)
import os
import sys
import subprocess
import time
import shutil
import stat
import xbmc
import xbmcaddon
import xbmcgui

addon = xbmcaddon.Addon(id='script.gateossteam.launcher')
addonPath = addon.getAddonInfo('path')
addonIcon = addon.getAddonInfo('icon')
addonVersion = addon.getAddonInfo('version')
dialog = xbmcgui.Dialog()
language = addon.getLocalizedString
scriptid = 'script.gateossteam.launcher'

steamLinux = addon.getSetting("SteamLinux").decode("utf-8")
kodiLinux = addon.getSetting("KodiLinux").decode("utf-8")
steamWin = addon.getSetting("SteamWin").decode("utf-8")
kodiWin = addon.getSetting("KodiWin").decode("utf-8")
steamOsx = addon.getSetting("SteamOsx").decode("utf-8")
kodiOsx = addon.getSetting("KodiOsx").decode("utf-8")
delUserScriptSett = addon.getSetting("DelUserScript")
quitKodiSetting = addon.getSetting("QuitKodi")
busyDialogTime = int(addon.getSetting("BusyDialogTime"))
scriptUpdateCheck = addon.getSetting("ScriptUpdateCheck")
filePathCheck = addon.getSetting("FilePathCheck")
kodiPortable = addon.getSetting("KodiPortable")
preScriptEnabled = addon.getSetting("PreScriptEnabled")
preScript = addon.getSetting("PreScript").decode("utf-8")
postScriptEnabled = addon.getSetting("PostScriptEnabled")
postScript = addon.getSetting("PostScript").decode("utf-8")
osWin = xbmc.getCondVisibility('system.platform.windows')
osOsx = xbmc.getCondVisibility('system.platform.osx')
osLinux = xbmc.getCondVisibility('system.platform.linux')
osAndroid = xbmc.getCondVisibility('system.platform.android')
wmctrlCheck = addon.getSetting("WmctrlCheck")
suspendAudio = addon.getSetting("SuspendAudio")

#HACK: sys.getfilesystemencoding() is not supported on all systems (e.g. Android)
txt_encode = 'utf-8'
try:
	txt_encode = sys.getfilesystemencoding()
except:
	pass
#osAndroid returns linux + android
if osAndroid: 
	osLinux = 0
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


def makeScriptExec():
	scriptPath = os.path.join(getAddonDataPath(), 'scripts', 'steam-launch.sh')
	if os.path.isfile(scriptPath):
		if not stat.S_IXUSR & os.stat(scriptPath)[stat.ST_MODE]:
			log('steam-launch.sh not executable: %s' % scriptPath)
			try:
				os.chmod(scriptPath, stat.S_IRWXU)
				log('steam-launch.sh now executable: %s' % scriptPath)
			except:
				log('ERROR: unable to make steam-launch.sh executable, exiting: %s' % scriptPath)
				dialog.notification(language(50123), language(50126), addonIcon, 5000)
				sys.exit()
			log('steam-launch.sh executable: %s' % scriptPath)


def kodiBusyDialog():
        xbmc.executebuiltin("ActivateWindow(busydialog)")
        log('busy dialog started')
        time.sleep(10)
        xbmc.executebuiltin("Dialog.Close(busydialog)")


def launchSteam():
	basePath = os.path.join(getAddonDataPath(), 'scripts')
        steamlauncher = os.path.join(basePath, 'steam-launch.sh')
        cmd = '"%s"' % (steamlauncher)
	try:
		print suspendAudio
		log('attempting to launch: %s' % cmd)
		print cmd.encode('utf-8')
                subprocess.Popen(cmd.encode(txt_encode), shell=True, close_fds=True)
                kodiBusyDialog()
	except:
		log('ERROR: failed to launch: %s' % cmd)
		print cmd.encode(txt_encode)
		dialog.notification(language(50123), language(50126), addonIcon, 5000)


log('****Running Steam-Launcher v%s....' % addonVersion)
log('foreseen ONLY to run on gateOS')
log('System text encoding in use: %s' % txt_encode)

makeScriptExec()
launchSteam()