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
# Add post-logon configuration script
#
cat - > /target/usr/bin/post_logon.sh << 'EOF'
#! /bin/bash

sudo apt-get update
sudo apt-get install apt-transport-https deb-multimedia-keyring openbox kodi kodi-standalone kodi-pvr-iptvsimple qjoypad unclutter

if [[ "$UID" -ne "0" ]]
then
  #
  # Wait up to 10 seconds and see if we have a connection. If not, pop the network dialog
  #
  nm-online -t 10 -q
  if [ "$?" -ne "0" ]; then
    while true;
    do
      zenity --info --title="SteamOS Install" --text="SteamOS cannot connect to the internet. An internet connection is required to continue installation. If you have a wireless network, configure it now."
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
(for i in `dkms status | cut -d, -f1-2 | tr , / | tr -d ' '`; do sudo dkms remove $i --all; done) | zenity --progress --no-cancel --pulsate --auto-close --text="Configuring Kernel Modules" --title="SteamOS Installation"
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

#
# gateOS adding udev-rule for allowing gamepads on new xserver sessions
#
cat - > /target/etc/udev/rules.d/99-joystick.rules << 'EOF'
KERNEL=="event*", ENV{ID_INPUT_JOYSTICK}=="?*", MODE:="0644"
EOF

#
# gateOS set autostart options for openbox, which is essentially set background black and start kodi fullscreen
#
mkdir -p /target/home/steam/.config/openbox/
cat - > /target/home/steam/.config/openbox/autostart << 'EOF'
xsetroot -solid black &
kodi -fs &
# check if desktop mode switch has been triggered. Do so if not
if [ -f /usr/share/xsessions/killsteam.desktop ]
    sudo /usr/bin/gateos_xsession_switch
fi    
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
# copy std gnome session to seperate file
cp /usr/share/xsessions/gnome.desktop /usr/share/xsessions/gnome_gateos.desktop
cp /usr/share/xsessions/killsteam.desktop /usr/share/xsessions/gnome.desktop
rm /usr/share/xsessions/killsteam.desktop
EOF

#
# gateOS enable anyone to sudo the steam terminate script
#
echo ALL ALL=NOPASSWD: /usr/bin/terminatesteam > /target/etc/sudoers.d/terminatesteam

#
# gateOS enable anyone to sudo the xsession switcher
#
echo ALL ALL=NOPASSWD: /usr/bin/gateos_xsession_switch > /target/etc/sudoers.d/gateos_xsession_switch

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
