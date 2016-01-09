# openwrt-on-android
Openwrt on Android

-----------------------------------------------------

Email: android_openwrt@sina.com


QQ:    3080001935


Skype: android_openwrt

-----------------------------------------------------

Directory description:



apk


--android-large.apk, transmission, aria2/yaaw, owncloud, phpBB, minidlna, samba, xunlei, baidu


--android-small.apk, same as larger one except owncloud, phpBB



build


--Files needed while build android openwrt image



files


--Files that modified or add to android openwrt image

-----------------------------------------------------

How to build android openwrt ipk.

1. git clone git://git.openwrt.org/15.05/openwrt.git


2. ./scripts/feeds update –a

   ./scripts/feeds install –a


3. Override openwrt source code with files in "build" directory


4. make ARCH=arm menuconfig


5. make V=s

