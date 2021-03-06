#! /bin/sh
# script runs after debian installer has done its thing

chroot /target adduser --gecos "" --disabled-password steam
chroot /target usermod -a -G desktop,audio,dip,video,plugdev,netdev,bluetooth,pulse-access steam
chroot /target usermod -a -G pulse-access desktop
cat - > /target/usr/share/lightdm/lightdm.conf.d/20_steamos.conf << 'EOF'
[SeatDefaults]
pam-service=lightdm-autologin
autologin-user=steam
autologin-user-timeout=0
EOF

cp -r /cdrom/recovery /target/boot > /target/var/log/post_install.log
mv /target/boot/recovery/live /target/boot/recovery/live-hd
chroot /target date > /target/etc/skel/.imageversion
cp /target/etc/skel/.imageversion /target/home/steam/.imageversion

#
# gateOS: Add preconfiguration data for steam user (preinstalled kodi addons for instance...)
#
cp /cdrom/gateOS/steamuserconfig.tar.gz /target/home/steam/

#
# Add post-logon configuration script
#
cat - > /target/usr/bin/post_logon.sh << 'EOF'
#! /bin/bash

######################################## gateOS begin ###################################################################
# install gateOS packages
(sudo apt-get update -y -q) | zenity --progress --no-cancel --pulsate --auto-close --text="Updating Package Sources" --title="gateOS Installation"
(sudo apt-get -y --force-yes install apt-transport-https deb-multimedia-keyring) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing Keyring of Multimedia Repository" --title="gateOS Installation"
(sudo apt-get -y --force-yes install openbox kodi kodi-standalone kodi-pvr-iptvsimple qjoypad unclutter python-pip gzip xautomation xdotool pcsxr mupen64plus lib32gcc1 gdebi-core p7zip git) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing additional packages" --title="gateOS Installation" 
(sudo apt-get -y --force-yes install google-chrome-stable) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing Google Chrome Browser" --title="gateOS Installation"

# gateOS: change Xwrapper.conf from console to anybody
sudo sed -i 's/console/anybody/g' /etc/X11/Xwrapper.config

# gateOS: remove chrome repository from sources.list after installation due it will put an own repo into sources.d
sudo sed -i 's,deb http://dl.google.com/linux/chrome/deb/ stable main,#removed,g' /etc/apt/sources.list
(sudo apt-get update -y -q) | zenity --progress --no-cancel --pulsate --auto-close --text="Updating Package Sources" --title="gateOS Installation"

# gateOS: install playonlinux
(sudo apt-get -y --force-yes install playonlinux) | zenity --progress --no-cancel --pulsate --auto-close --text="Initial installation of PlayOnLinux (will fail as expected)" --title="gateOS Installation"
(sudo rm -f /usr/share/doc/libattr1/changelog.Debian.gz) | zenity --progress --no-cancel --pulsate --auto-close --text="Fixing expected dependency errors" --title="gateOS Installation"
(sudo apt-get install -f -y --force-yes) | zenity --progress --no-cancel --pulsate --auto-close --text="Fixing expected dependency errors" --title="gateOS Installation"
(sudo apt-get -y --force-yes install playonlinux) | zenity --progress --no-cancel --pulsate --auto-close --text="Final installation of PlayOnLinux" --title="gateOS Installation"

# gateOS: install steamoscontroller useland driver
(sudo pip install libusb1) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing SteamController standalone driver dependencies" --title="gateOS Installation"
(sudo pip install enum34) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing SteamController standalone driver dependencies" --title="gateOS Installation"
(wget https://github.com/ynsta/steamcontroller/archive/master.tar.gz) | zenity --progress --no-cancel --pulsate --auto-close --text="Downloading SteamController standalone driver" --title="gateOS Installation"
(tar xf master.tar.gz) | zenity --progress --no-cancel --pulsate --auto-close --text="Extract SteamController standalone driver" --title="gateOS Installation"
cd steamcontroller-master
(sudo python setup.py install) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing SteamController standalone driver" --title="gateOS Installation"

# gateOS install pcsx2 emulator get single package from launchpad ppa for ubuntu (dirty I know...) => Will also need a bios solution. Just to have it on board at the moment
(wget https://launchpad.net/~gregory-hainaut/+archive/ubuntu/pcsx2.official.ppa/+files/pcsx2_1.4.0-1_i386.deb) | zenity --progress --no-cancel --pulsate --auto-close --text="Downloading single pcsx2 package" --title="gateOS Installation"
(sudo gdebi -n pcsx2_1.4.0-1_i386.deb) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing single pcsx2 package and its dependencies" --title="gateOS Installation"

# gateOS install SteamCMD
sudo mkdir /home/steam/.steamcmd
sudo cd /home/steam/.steamcmd
(sudo wget https://steamcdn-a.akamaihd.net/client/installer/steamcmd_linux.tar.gz) | zenity --progress --no-cancel --pulsate --auto-close --text="Downloading SteamCMD" --title="gateOS Installation"
(sudo tar -xvzf steamcmd_linux.tar.gz) | zenity --progress --no-cancel --pulsate --auto-close --text="Extract SteamCMD" --title="gateOS Installation"
sudo chown -R steam:steam /home/steam/.steamcmd
cd /home/desktop/

# gateOS install dolphin-emu (Nintendo Gamecube) => needs to be compiled...
sudo mkdir /home/steam/.builddolphin
sudo cd /home/steam/.builddolphin
(sudo git clone https://github.com/dolphin-emu/dolphin.git) | zenity --progress --no-cancel --pulsate --auto-close --text="Cloning dolphin-emu repository" --title="gateOS Installation"
(sudo apt-get install cmake g++ libwxbase3.0-dev libwxgtk3.0-dev libgtk2.0-dev libbluetooth-dev libxrandr-dev libxext-dev libreadline-dev libpulse-dev libusb-1.0-0-dev)| zenity --progress --no-cancel --pulsate --auto-close --text="Installing dependencies for compiling dolphin-emu" --title="gateOS Installation"
sudo cd /home/steam/.builddolphin/dolphin
sudo mkdir /home/steam/.builddolphin/dolphin/Build
sudo cd /home/steam/.builddolphin/dolphin/Build
(sudo cmake ..)| zenity --progress --no-cancel --pulsate --auto-close --text="Configuring dolphin-emu" --title="gateOS Installation"
(sudo make) | zenity --progress --no-cancel --pulsate --auto-close --text="Compiling dolphin-emu" --title="gateOS Installation"
(sudo make install) | zenity --progress --no-cancel --pulsate --auto-close --text="Installing compiled dolphin-emu" --title="gateOS Installation"

######################################## gateOS end ###################################################################

if [[ "$UID" -ne "0" ]]
then
  #
  # Wait up to 10 seconds and see if we have a connection. If not, pop the network dialog
  #
  nm-online -t 10 -q
  if [ "$?" -ne "0" ]; then
    while true;
    do
      zenity --info --title="gateOS Install" --text="gateOS cannot connect to the internet. An internet connection is required to continue installation. If you have a wireless network, configure it now."
      nm-connection-editor --type=802-11-wireless --show
      nm-online -t 30
      if [ "$?" -eq "0" ]; then 
        break
      fi
      echo "Still waiting for internet connection..."
    done
  fi

  # dummy file to skip the Steam Install Agreement dialog
  touch ~/.steam/steam_install_agreement.txt
  # pass -exitsteam so steam doesn't actually run after bootstrapping
  steam -exitsteam
  rm ~/.steam/starting
  cp ~/.local/share/Steam/steam_install_agreement.txt ~/.steam/steam_install_agreement.txt
  sudo /usr/bin/post_logon.sh
  exit
fi
dbus-send --system --type=method_call --print-reply --dest=org.freedesktop.Accounts /org/freedesktop/Accounts/User1000 org.freedesktop.Accounts.User.SetXSession string:gnome
dbus-send --system --type=method_call --print-reply --dest=org.freedesktop.Accounts /org/freedesktop/Accounts/User1001 org.freedesktop.Accounts.User.SetXSession string:openbox
systemctl enable build-dkms
(for i in `dkms status | cut -d, -f1-2 | tr , / | tr -d ' '`; do sudo dkms remove $i --all; done) | zenity --progress --no-cancel --pulsate --auto-close --text="Configuring Kernel Modules" --title="gateOS Installation"
plymouth-set-default-theme -R steamos
update-grub
grub-set-default 0
# boot into recovery partition on the next boot
grub-reboot "Capture System Partition"
passwd --delete desktop
rm /etc/sudoers.d/post_logon
rm /usr/bin/post_logon.sh && reboot
rm /home/steam/.config/autostart/post_logon.desktop
EOF

chmod +x /target/usr/bin/post_logon.sh

######################################## gateOS begin ###################################################################

#
# gateOS adding udev-rule for allowing gamepads on new xserver sessions
#
cat - > /target/etc/udev/rules.d/99-joystick.rules << 'EOF'
KERNEL=="event*", ENV{ID_INPUT_JOYSTICK}=="?*", MODE:="0644"
EOF

#
# gateOS adding udev-rule for useland steamcontroller driver
#
cat - > /target/etc/udev/rules.d/99-steam-controller.rules << 'EOF'
SUBSYSTEM=="usb", ATTRS{idVendor}=="28de", GROUP="steam", MODE="0660"
KERNEL=="uinput", MODE="0660", GROUP="steam", OPTIONS+="static_node=uinput"
EOF

#
# gateOS set autostart options for openbox, which is essentially set background black and start kodi fullscreen
#
mkdir -p /target/home/steam/.config/openbox/
cat - > /target/home/steam/.config/openbox/autostart << 'EOF'
xsetroot -solid black &
sc-desktop.py start &
kodi -fs &
sudo /usr/bin/gateos_xsession_switch
EOF

chmod +x /target/home/steam/.config/openbox/autostart

#
# gateOS add steam terminate script
#
cat - > /target/usr/bin/terminatesteam << 'EOF'
#!/bin/sh

killall -9 steam
killall -9 steamcompmgr
killall steam
killall steamcompmgr
EOF

chmod +x /target/usr/bin/terminatesteam

#
# gateOS changes for enabling returning to kodi when selecting switching to desktop mode
#

# kill steamos session script -> will be triggered by fake gnome xsession
cat - > /target/usr/bin/kill-steamos-session << 'EOF'
#!/bin/sh
sudo /usr/bin/terminatesteam
/usr/bin/returntosteam.sh
EOF

chmod +x /target/usr/bin/kill-steamos-session

# template for fake gnome session -> desktop mode should be handled by kodi later on
cat - > /target/usr/share/xsessions/killsteam.desktop << 'EOF'
[Desktop Entry]
Name=Fake GNOME
Comment=Fake GNOME Session that aims to kill steam
Exec=kill-steamos-session
TryExec=kill-steamos-session
Icon=
Type=Application
EOF

# script for triggering exchanging gnome desktop mode session with fake gnome session
cat - > /target/usr/bin/gateos_xsession_switch << 'EOF'
#!/bin/sh
# copy std gnome session to seperate file
mv /usr/share/xsessions/gnome.desktop /usr/share/xsessions/gnome_gateos.desktop
cp /usr/share/xsessions/killsteam.desktop /usr/share/xsessions/gnome.desktop
rm /usr/share/xsessions/killsteam.desktop
# finally remove calling this script from autostart
sed -i 's,sudo /usr/bin/gateos_xsession_switch,#removed,g' /home/steam/.config/openbox/autostart
EOF

chmod +x /target/usr/bin/gateos_xsession_switch

#
# gateOS enable anyone to sudo the steam terminate script
#
echo ALL ALL=NOPASSWD: /usr/bin/terminatesteam > /target/etc/sudoers.d/terminatesteam

#
# gateOS enable anyone to sudo the xsession switcher
#
echo ALL ALL=NOPASSWD: /usr/bin/gateos_xsession_switch > /target/etc/sudoers.d/gateos_xsession_switch

######################################## gateOS end ###################################################################

#
# Enable anyone to sudo the post logon script
#
echo ALL ALL=NOPASSWD: /usr/bin/post_logon.sh > /target/etc/sudoers.d/post_logon

#
# Set post logon to run at the first logon
#
cat - > /target/home/steam/.config/autostart/post_logon.desktop << 'EOF'
[Desktop Entry]
Type=Application
Exec=/usr/bin/post_logon.sh
X-GNOME-Autostart-enabled=true
Name=postlogon
EOF

#
# Run aticonfig if an AMD card is present
#
if [ -n "$(lspci|grep VGA|grep -i 'AMD\|ATI')" ]; then
	if [ ! -n "$(lspci|grep VGA|grep NVIDIA)" ]; then
		chroot /target update-alternatives --set glx /usr/lib/fglrx
	fi
fi

#
# Boot splash screen and GRUB configuration
#
if test `/target/bin/grep -A10000 "### BEGIN /etc/grub.d/30_os-prober ###" /target/boot/grub/grub.cfg | /target/bin/grep -B10000 "### END /etc/grub.d/30_os-prober ###" | wc -l` -gt 4; then
ISDUALBOOT=Y
else
ISDUALBOOT=N
fi
cat - > /target/etc/default/grub << EOF
# If you change this file, run 'update-grub' afterwards to update
# /boot/grub/grub.cfg.
# For full documentation of the options in this file, see:
#   info -f grub -n 'Simple configuration'

GRUB_DEFAULT=saved
GRUB_HIDDEN_TIMEOUT_QUIET=true
GRUB_DISTRIBUTOR=\`lsb_release -i -s 2> /dev/null || echo Debian\`
GRUB_CMDLINE_LINUX=""
GRUB_BACKGROUND=/usr/share/plymouth/themes/steamos/steamos_branded.png
GRUB_DISABLE_LINUX_RECOVERY="true"
GRUB_GFXMODE=auto
EOF
if test "${ISDUALBOOT}" = N; then
echo "GRUB_TIMEOUT=0" >> /target/etc/default/grub
echo "GRUB_HIDDEN_TIMEOUT=1" >> /target/etc/default/grub
else
echo "GRUB_TIMEOUT=5" >> /target/etc/default/grub
fi


# Add system partition backup/restore to the boot menu
RECOVERYPARTITION=`mount | grep "/target/boot/recovery " | cut -f1 -d' '`
ROOTPARTITION=`mount | grep "/target " | cut -f1 -d' ' | cut -f3- -d'/'`
SWAPPARTITION=`tail -1 /proc/swaps | cut -f1 -d' '`
if test -n "${RECOVERYPARTITION}" && test -n "${ROOTPARTITION}" && test -n "${SWAPPARTITION}"; then
if test -d /sys/firmware/efi/; then
ISEFI=Y
else
ISEFI=N
fi

# enable splash and set framebuffer size to 1024x768x24 for non-efi systems
if test "${ISEFI}" = "Y"; then
echo "GRUB_CMDLINE_LINUX_DEFAULT=\"quiet splash\"" >> /target/etc/default/grub
else
echo "GRUB_CMDLINE_LINUX_DEFAULT=\"quiet splash vga=0x0318\"" >> /target/etc/default/grub
fi

cat - >> /target/etc/grub.d/40_custom << EOF
menuentry "Capture System Partition"{
  search --set -f /live-hd/vmlinuz
EOF
if test "${ISEFI}" = "Y"; then
echo "  fakebios" >> /target/etc/grub.d/40_custom
fi
cat - >> /target/etc/grub.d/40_custom << EOF
  linux /live-hd/vmlinuz boot=live union=overlay username=user config components quiet noswap edd=on nomodeset nodmraid noeject noprompt locales="en_US.UTF-8" keyboard-layouts=NONE ocs_prerun="mount ${RECOVERYPARTITION} /home/partimag" ocs_live_run="ocs-sr -q2 -j2 -z1p -i 2000 -sc -p true saveparts steambox ${ROOTPARTITION}" ocs_live_extra_param="" ocs_live_batch=no vga=788 ip=frommedia   live-media-path=/live-hd bootfrom=${SWAPPARTITION} toram=filesystem.squashfs i915.blacklist=yes radeonhd.blacklist=yes nouveau.blacklist=yes vmwgfx.enable_fbdev=no
  initrd /live-hd/initrd.img
}
menuentry "Restore System Partition"{
  search --set -f /live-hd/vmlinuz
EOF
if test "${ISEFI}" = "Y"; then
echo "  fakebios" >> /target/etc/grub.d/40_custom
fi
cat - >> /target/etc/grub.d/40_custom << EOF
  linux /live-hd/vmlinuz boot=live union=overlay username=user config components quiet noswap edd=on nomodeset nodmraid noeject noprompt locales="en_US.UTF-8" keyboard-layouts=NONE ocs_prerun="mount ${RECOVERYPARTITION} /home/partimag" ocs_live_run="ocs-sr -e1 auto -e2 -r -j2 -k -p reboot restoreparts steambox ${ROOTPARTITION}" ocs_live_extra_param="" ocs_live_batch=no vga=788 ip=frommedia   live-media-path=/live-hd bootfrom=${SWAPPARTITION} toram=filesystem.squashfs i915.blacklist=yes radeonhd.blacklist=yes nouveau.blacklist=yes vmwgfx.enable_fbdev=no
  initrd /live-hd/initrd.img
}
menuentry "Clonezilla live"{
  search --set -f /live-hd/vmlinuz
EOF
if test "${ISEFI}" = "Y"; then
echo "  fakebios" >> /target/etc/grub.d/40_custom
fi
cat - >> /target/etc/grub.d/40_custom << EOF
  linux /live-hd/vmlinuz boot=live union=overlay username=user config components quiet noswap edd=on nomodeset nodmraid noeject noprompt locales="en_US.UTF-8" keyboard-layouts=NONE ocs_prerun="mount ${RECOVERYPARTITION} /home/partimag" ocs_live_run="ocs-live-general" ocs_live_extra_param="" ocs_live_batch=no vga=788 ip=frommedia  nosplash  live-media-path=/live-hd bootfrom=${SWAPPARTITION} toram=filesystem.squashfs i915.blacklist=yes radeonhd.blacklist=yes nouveau.blacklist=yes vmwgfx.enable_fbdev=no
  initrd /live-hd/initrd.img
}
EOF
else
echo "Missing one of /, /boot/recovery, or swap. Disabling recovery partition support"
fi
