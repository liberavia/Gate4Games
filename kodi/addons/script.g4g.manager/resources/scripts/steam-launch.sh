#!/bin/bash

killall -9 kodi.bin
sc-desktop.py stop
startx /usr/bin/steamos-session
sc-desktop.py start
/usr/bin/kodi -fs