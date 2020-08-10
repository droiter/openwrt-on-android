# Droiter -- Openwrt on Android

Support Android 9.0 magisk now, you can download latest version from github release section.
    
##### One look is worth a thousand words：
https://www.youtube.com/watch?v=_ReqlwvR5_0

##### Google Play store：
https://play.google.com/store/apps/details?id=org.andwrt


####
  - QQ:    3080001935
  - Skype: android_openwrt
  - Email: android_openwrt@sina.com

## Directory description:
#### apk
- android-large.apk, transmission, aria2/yaaw, owncloud(port 9800), phpBB(port 58888), minidlna, samba, xunlei, baidu
- android-small.apk, same as larger one except owncloud, phpBB

#### build
- Files needed while build android openwrt image

#### files
- Files that modified or add to android openwrt image

## How to build android openwrt ipk.
1. git clone git://git.openwrt.org/15.05/openwrt.git
2. ./scripts/feeds update –a
3. ./scripts/feeds install –a
4. Overwrite openwrt source code with files in "build" directory
5. make ARCH=arm menuconfig
6. make V=s

## Security issues

1. Security is traded for convenience, all account password is initialized as "root“ and "asdf1234" in login/wifi/samba/mysqld etc.
2. Please change the password, or use it with your own risk.
3. There's a cron background job try to update everyday, you might stop it

# Blog
https://herabox.org

# 中文博客

http://blog.sina.com.cn/u/5384121684

