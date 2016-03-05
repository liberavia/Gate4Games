 #!/bin/bash
killall -9 kodi.bin
qjoypad DesktopMode &
# starting the overlay
# ~/.kodi/addons/script.gateoschrome.launcher/resources/scripts/overlay_trigger.py
PID_OL_TRIGGER=$!
/usr/bin/google-chrome-stable -kiosk https://ixquick.com/
killall -9 qjoypad
kill -9 $PID_OL_TRIGGER
/usr/bin/kodi -fs