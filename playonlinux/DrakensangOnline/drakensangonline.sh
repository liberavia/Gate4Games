#!/bin/bash
# Date : 2015-11-22 21-11
# Last revision : 2012-09-25 22-23
# Wine version used : 1.7.55
# Distribution used to test : Ubuntu 14.04.3
# Author : liberavia
 
[ "$PLAYONLINUX" = "" ] && exit 0
source "$PLAYONLINUX/lib/sources"
 
TITLE="Drakensang Online"
PREFIX="drakensangonline"
 
POL_GetSetupImages "http://www.gate4games.com/pol/resources/$PREFIX/top.jpg" "http://www.gate4games.com/pol/resources/$PREFIX/left.jpg" "$TITLE"
POL_SetupWindow_Init
POL_SetupWindow_presentation "$TITLE" "Bigpoint" "https://www.bigpoint.net/" "liberavia" "$PREFIX"
POL_Wine_SelectPrefix "$PREFIX"
POL_Wine_PrefixCreate "1.7.55"
 
# Ask for account and help user for creating a new one if not done yet 
POL_SetupWindow_menu "$(eval_gettext 'You will need a Drakensang Online account for playing. What do you want to do?')" "$(eval_gettext 'Drakensang Online Account')" "$(eval_gettext 'Continue installation and register an account.')|$(eval_gettext 'No, thanks, I already have an account.')" "|" 

if [ "$APP_ANSWER" == "$(eval_gettext 'Continue installation and register an account.')" ] 
then
    POL_Browser "http://www.drakensang.de/home/signup"
fi
 
# Choose installation method
POL_SetupWindow_InstallMethod "LOCAL,DOWNLOAD"
 
if [ "$INSTALL_METHOD" = "LOCAL" ]
then
    # Let the user choose the installer from local place
    cd "$HOME"
    POL_SetupWindow_browse "$(eval_gettext 'Please select the setup file to run')" "$TITLE"
    SETUP_EXE="$APP_ANSWER"
elif [ "$INSTALL_METHOD" = "DOWNLOAD" ]
then
    # Download client directly
    POL_System_TmpCreate "$PREFIX"
    cd "$POL_System_TmpDir"
    POL_Download "http://drasaonline-481.level3.bpcdn.net/applet/dro_setup.exe"
    SETUP_EXE="$PWD/ro_setup.exe"
fi 

# Install needed libraries, extras etc.
POL_Call POL_Install_tahoma
POL_Call POL_Install_tahoma2
POL_Install_corefonts

# Configure wine
# => Currently not needed

# Install program
POL_Wine_WaitBefore "$TITLE"
POL_Wine "$POL_System_TmpDir/dro_setup.exe"
POL_System_TmpDelete



POL_Shortcut "thinclient.exe" "$TITLE" "$TITLE.png"
  
POL_SetupWindow_Close
exit