#!/usr/bin/env python
# -*- coding:utf-8 -*-
####################################################################################################
#
#  Author: WishInLife
#  QQ: 57956720
#  QQ Group: 59160264
#  E-Mail: wishinlife@qq.com
#  Web Home: http://www.syncy.cn
#  Update date: 2015-09-12
#  VERSION: 2.5.3
#  Required packages: kmod-nls-utf8, libopenssl, libcurl, python, python-curl, python-crypto
#  If import python-crypto package, SyncY can support ARC4、Blowfish and AES encryption.
#
####################################################################################################

import sys
import os
import stat
import time
import re
import struct
import hashlib
from urllib import urlencode
import threading
import traceback
import json
import random
# import fcntl
# if '/usr/lib/python2.7/site-packages' not in sys.path:
#    sys.path.append('/usr/lib/python2.7/site-packages')
import pycurl
import binascii
# import zlib
# import fileinput
try:    # require python-crypto
    from Crypto.Cipher import ARC4
    from Crypto.Cipher import Blowfish
    from Crypto.Cipher import AES
except ImportError, ex:
    ARC4 = Blowfish = AES = None

# set config_file and pidfile for your config storage path.
if os.name == 'nt':
    __CONFIG_FILE__ = './syncy'
    __PIDFILE__ = './syncy.pid'
    __CHARSET__ = 'GBK'    # windows charset
    __TMP_DIR__ = os.environ['TMP'].replace('\\', '/')
else:
    __CONFIG_FILE__ = '/etc/config/syncy'
    __PIDFILE__ = '/var/run/syncy.pid'
    __CHARSET__ = 'UTF-8'  # linux charset
    __TMP_DIR__ = '/tmp'

if sys.getdefaultencoding() != __CHARSET__:
    reload(sys)
    sys.setdefaultencoding(__CHARSET__)

#  Don't modify the following.
__VERSION__ = '2.5.3'
__DEBUG__ = False
__author__ = "WishInLife <wishinlife@qq.com>"

if os.name == 'nt':
    import win32con
    import win32file
    import pywintypes
    LOCK_SH = 0
    LOCK_NB = win32con.LOCKFILE_FAIL_IMMEDIATELY
    LOCK_EX = win32con.LOCKFILE_EXCLUSIVE_LOCK
    LOCK_UN = 8
    __overlapped = pywintypes.OVERLAPPED()

    def flock(fd, op):
        fh = win32file._get_osfhandle(fd.fileno())
        if op == LOCK_UN:
            return win32file.UnlockFileEx(fh, 0, 0x0fff0000, __overlapped)
        else:
            return win32file.LockFileEx(fh, op, 0, 0x0fff0000, __overlapped)

    def lockf(fd, op, length=0, start=0, whence=0):
        fh = win32file._get_osfhandle(fd.fileno())
        fsize = win32file.GetFileSize(fh)
        if whence == 1:
            start += fd.tell()
        elif whence == 2:
            start += fsize
        if length == 0:
            length = fsize
        int32 = 2 ** 32
        if op == LOCK_UN:
            return win32file.UnlockFile(fh, int(start % int32), int(start / int32), int(length % int32), int(length / int32))
        else:
            return win32file.LockFile(fh, int(start % int32), int(start / int32), int(length % int32), int(length / int32))
else:
    from fcntl import LOCK_EX, LOCK_SH, LOCK_NB, LOCK_UN, flock, lockf

LogLock = threading.Lock()
def printlog(msg):
    LogLock.acquire()
    print(msg)
    LogLock.release()

def rename(src, dst):
    if os.name == 'nt' and os.path.exists(dst):
        os.remove(dst)
    os.rename(src, dst)

class SyncY:
    synccount = 0
    errorcount = 0
    failcount = 0
    EXLock = threading.Lock()
    TaskSemaphore = None
    oldSTDERR = None
    oldSTDOUT = None
    syncydb = None
    sydb = None
    sydblen = None
    syncData = None
    basedirlen = None
    syncpath = {}
    extraslice = None
    encryption = None
    encryptkey = ''
    stop = False
    config = {
        'syncylog'		: '',
        'blocksize'		: 10,
        'ondup'			: 'rename',
        'datacache'		: 'on',
        'excludefiles'	: '',
        'listnumber'	: 100,
        'retrytimes'	: 3,
        'retrydelay'	: 3,
        'maxsendspeed'	: 0,
        'maxrecvspeed'	: 0,
        'speedlimitperiod': '0-0',
        'syncperiod'	: '0-24',
        'syncinterval'	: 3600,
        'tasknumber'	: 2,
        'threadnumber'	: 2}
    syre = {
        'newname': re.compile(r'^(.*)(\.[^.]+)$'),
        'pcspath': re.compile(r'^[\s\.\n].*|.*[/<>\\|\*\?:\"].*|.*[\s\.\n]$')}
    syncytoken = {'synctotal': 0}
    pcsroot = '/apps/SyncY'
    synctask = {}

    def __init__(self, argv=sys.argv[1:]):
        self.__argv = argv
        if len(self.__argv) == 0 or self.__argv[0] in ['compress', 'convert', 'rebuild']:
            if os.path.exists(__PIDFILE__):
                with open(__PIDFILE__, 'r') as pidh:
                    mypid = pidh.read()
                try:
                    os.kill(int(mypid), 0)
                except os.error:
                    pass
                else:
                    print("SyncY is running!")
                    sys.exit(0)
            with open(__PIDFILE__, 'w') as pidh:
                pidh.write(str(os.getpid()))
        if not (os.path.isfile(__CONFIG_FILE__)):
            sys.stderr.write('%s ERROR: Config file "%s" does not exist.\n' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), __CONFIG_FILE__))
            sys.exit(2)

        with open(__CONFIG_FILE__, 'r') as sycfg:
            line = sycfg.readline()
            section = ''
            while line:
                if re.findall(r'^\s*#', line) or re.findall(r'^\s*$', line):
                    line = sycfg.readline()
                    continue
                line = re.sub(r'#[^\']*$', '', line)
                m = re.findall(r'\s*config\s+([^\s]+).*', line)
                if m:
                    section = m[0].strip('\'')
                    if section == 'syncpath':
                        SyncY.syncpath[str(len(SyncY.syncpath))] = {}
                    line = sycfg.readline()
                    continue
                m = re.findall(r'\s*option\s+([^\s]+)\s+\'([^\']*)\'', line)
                if m:
                    if section == 'syncy':
                        if m[0][0].strip('\'') in ['blocksize', 'listnumber', 'syncinterval', 'threadnumber', 'tasknumber', 'retrytimes', 'retrydelay']:
                            SyncY.config[m[0][0].strip('\'')] = int(m[0][1])
                        elif m[0][0].strip('\'') in ['maxsendspeed', 'maxrecvspeed']:
                            if m[0][1].upper().find('K') > -1:
                                idx = m[0][1].upper().find('K')
                                SyncY.config[m[0][0].strip('\'')] = int(m[0][1][0:idx]) * 1024
                            elif m[0][1].upper().find('M') > -1:
                                idx = m[0][1].upper().find('M')
                                SyncY.config[m[0][0].strip('\'')] = int(m[0][1][0:idx]) * 1024 * 1024
                            else:
                                SyncY.config[m[0][0].strip('\'')] = int(m[0][1])
                        else:
                            SyncY.config[m[0][0].strip('\'')] = m[0][1]
                    elif section == 'syncytoken':
                        if m[0][0].strip('\'') in ['expires_in', 'refresh_date', 'compress_date', 'synctotal']:
                            SyncY.syncytoken[m[0][0].strip('\'')] = int(m[0][1])
                        else:
                            SyncY.syncytoken[m[0][0].strip('\'')] = m[0][1]
                    elif section == 'syncpath':
                        if m[0][0].strip('\'') == 'remotepath':
                            if m[0][1].lower().startswith('/apps/syncy'):
                                SyncY.syncpath[str(len(SyncY.syncpath) - 1)]['remotepath'] = m[0][1][11:]
                            elif m[0][1].lower().startswith('/我的应用程序/syncy'):
                                SyncY.syncpath[str(len(SyncY.syncpath) - 1)]['remotepath'] = m[0][1][len('/我的应用程序/syncy'):]
                            else:
                                SyncY.syncpath[str(len(SyncY.syncpath) - 1)]['remotepath'] = m[0][1]
                        else:
                            SyncY.syncpath[str(len(SyncY.syncpath) - 1)][m[0][0].strip('\'')] = m[0][1].replace('\\', '/')
                line = sycfg.readline()
        if SyncY.config['syncylog'] != '':
            if not os.path.exists(os.path.dirname(SyncY.config['syncylog'])):
                os.makedirs(os.path.dirname(SyncY.config['syncylog']))
            if os.path.exists(SyncY.config['syncylog']) and os.path.isdir(SyncY.config['syncylog']):
                SyncY.config['syncylog'] = self.__catpath(SyncY.config['syncylog'], 'syncy.log')
                self.__save_config()
        if SyncY.oldSTDERR is None and SyncY.config['syncylog'] != '' and len(self.__argv) != 0 and self.__argv[0] in ['sybind', 'cpbind']:
            SyncY.oldSTDERR = sys.stderr
            SyncY.oldSTDOUT = sys.stdout
            sys.stderr = open(SyncY.config['syncylog'], 'a', 0)
            sys.stdout = sys.stderr

        if 'refresh_token' not in SyncY.syncytoken or SyncY.syncytoken['refresh_token'] == '' or (len(self.__argv) != 0 and self.__argv[0] in ['sybind', 'cpbind']):
            sycurl = SYCurl()
            if (('device_code' not in SyncY.syncytoken or SyncY.syncytoken['device_code'] == '') and len(self.__argv) == 0) or (len(self.__argv) != 0 and self.__argv[0] == 'sybind'):
                retcode, responses = sycurl.request('https://www.syncy.cn/syserver', {}, {'method': 'bind_device', 'scope': 'basic,netdisk'}, 'POST', SYCurl.Normal)
                responses = json.loads(responses)
                if retcode != 200 or 'error_code' in responses:
                    print('%s ERROR(Errno:%d): Get device code failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, responses['error_msg']))
                    sys.exit(3)
                device_code = responses['device_code']
                user_code = responses['user_code']
                if len(self.__argv) != 0 and self.__argv[0] == 'sybind':
                    with open(__TMP_DIR__ + '/syncy.bind', 'w') as sybind:
                        sybind.write('{"user_code":"%s","device_code":"%s","time":%d}' % (user_code, device_code, int(time.time())))
                    sys.exit(0)
                SyncY.syncytoken['device_code'] = device_code
                print('Device binding Guide:')
                print('     1. Open web browser to visit:"https://openapi.baidu.com/device" and input user code to binding your baidu account.')
                print(' ')
                print('     2. User code:\033[31m %s\033[0m' % user_code)
                print('     (User code valid for 30 minutes.)')
                print(' ')
#                raw_input('     3. After granting access to the application, come back here and press [Enter] to continue.')
#                print(' ')
            if len(self.__argv) != 0 and self.__argv[0] == 'cpbind':
                with open(__TMP_DIR__ + '/syncy.bind', 'r') as sybind:
                    bindinfo = sybind.read()
                bindinfo = json.loads(bindinfo)
                os.remove(__TMP_DIR__ + '/syncy.bind')
                if 'device_code' in bindinfo:
                    if int(time.time()) - int(bindinfo['time']) >= 1800:
                        sys.exit(4)
                    SyncY.syncytoken['device_code'] = bindinfo['device_code']
                else:
                    sys.exit(5)
            retcode, responses = sycurl.request('https://www.syncy.cn/syserver', {}, {'method': 'get_device_token', 'code': SyncY.syncytoken['device_code'], 'edition': 'python', 'ver': __VERSION__}, 'POST', SYCurl.Normal)
            responses = json.loads(responses)
            if retcode != 200 or 'error_code' in responses:
                print('%s ERROR(Errno:%d): Get device token failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, responses['error_msg']))
                sys.exit(6)
            if 'refresh_token' not in responses:
                print('You should bind user code on baidu')
                sys.exit(6)
            SyncY.syncytoken['refresh_token'] = responses['refresh_token']
            SyncY.syncytoken['access_token'] = responses['access_token']
            SyncY.syncytoken['expires_in'] = int(responses['expires_in'])
            SyncY.syncytoken['refresh_date'] = int(time.time())
            SyncY.syncytoken['compress_date'] = int(time.time())
            self.__save_config()
            if len(self.__argv) != 0 and self.__argv[0] == 'cpbind':
                sys.exit(0)
            print('%s INFO: Get device token success.\n' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
        if SyncY.oldSTDERR is None and SyncY.config['syncylog'] != '':
            print('%s INFO: Running log output to log file %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), SyncY.config['syncylog']))
            SyncY.oldSTDERR = sys.stderr
            SyncY.oldSTDOUT = sys.stdout
            sys.stderr = open(SyncY.config['syncylog'], 'a', 0)
            sys.stdout = sys.stderr

        try:
            if SyncY.config['blocksize'] < 1:
                SyncY.config['blocksize'] = 10
                print('%s WARNING: "blocksize" must great than or equal to 1(M), set to default 10(M).' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['ondup'] != 'overwrite' and SyncY.config['ondup'] != 'rename':
                SyncY.config['ondup'] = 'rename'
                print('%s WARNING: ondup is invalid, set to default(overwrite).' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['datacache'] != 'on' and SyncY.config['datacache'] != 'off':
                SyncY.config['datacache'] = 'on'
                print('%s WARNING: "datacache" is invalid, set to default(on).' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['retrytimes'] < 0:
                SyncY.config['retrytimes'] = 3
                print('%s WARNING: "retrytimes" is invalid, set to default(3 times).' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['retrydelay'] < 0:
                SyncY.config['retrydelay'] = 3
                print('%s WARNING: "retrydelay" is invalid, set to default(3 second).' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['listnumber'] < 1:
                SyncY.config['listnumber'] = 100
                print('%s WARNING: "listnumber" must great than or equal to 1, set to default 100.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['syncinterval'] < 0:
                SyncY.config['syncinterval'] = 3600
                print('%s WARNING: "syncinterval" must great than or equal to 1, set to default 3600.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['maxsendspeed'] < 0:
                SyncY.config['maxsendspeed'] = 0
                print('%s WARNING: "maxsendspeed" must great than or equal to 0, set to default 0.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['maxrecvspeed'] < 0:
                SyncY.config['maxrecvspeed'] = 0
                print('%s WARNING: "maxrecvspeed" must great than or equal to 0, set to default 100.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['threadnumber'] < 1:
                SyncY.config['threadnumber'] = 2
                print('%s WARNING: "threadnumber" must great than or equal to 1, set to default 2.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            if SyncY.config['tasknumber'] < 1:
                SyncY.config['tasknumber'] = 2
                print('%s WARNING: "tasknumber" must great than or equal to 1, set to default 2.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
            starthour, endhour = SyncY.config['speedlimitperiod'].split('-', 1)
            if starthour == '' or endhour == '' or int(starthour) < 0 or int(starthour) > 23 or int(endhour) < 0 or int(endhour) > 24:
                print('%s WARNING: "speedlimitperiod" is invalid, set to default(0-0), no limit.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
                SyncY.config['speedlimitperiod'] = '0-0'
            starthour, endhour = SyncY.config['syncperiod'].split('-', 1)
            if starthour == '' or endhour == '' or int(starthour) < 0 or int(starthour) > 23 or int(endhour) < 0 or int(endhour) > 24 or endhour == starthour:
                print('%s WARNING: "syncperiod" is invalid, set to default(0-24).' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
                SyncY.config['syncperiod'] = '0-24'
        except Exception, e:
            print('%s ERROR: initialize parameters failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), e, traceback.format_exc()))
            sys.exit(7)

        self._excludefiles = SyncY.config['excludefiles'].replace('\\', '/').replace('.', '\.').replace('*', '.*').replace('?', '.?').split(';')
        for i in xrange(len(self._excludefiles)):
            self._excludefiles[i] = re.compile(eval('r"^' + self._excludefiles[i] + '$"'))
        self._excludefiles.append(re.compile(r'^.*\.syy$'))

        if (SyncY.syncytoken['refresh_date'] + SyncY.syncytoken['expires_in'] - 864000) < int(time.time()):
            self.__check_expires()
        SyncY.TaskSemaphore = threading.Semaphore(SyncY.config['tasknumber'])
        size = 32768
        while True:
            try:
                threading.stack_size(size)
                threadtest = ThreadTest()
                threadtest.start()
                break
            except threading.ThreadError:
                threading.stack_size(0)
                break
            except RuntimeError:
                threading.stack_size(0)
                break
            except ValueError:
                if size < 512 * 1024:
                    size *= 2
                else:
                    threading.stack_size(0)
                    break

    def __del__(self):
        if self.__class__.oldSTDERR is not None:
            sys.stderr.flush()
            sys.stderr.close()
            sys.stderr = self.__class__.oldSTDERR
            sys.stdout = self.__class__.oldSTDOUT
        if os.path.exists(__PIDFILE__):
            with open(__PIDFILE__, 'r') as pidh:
                lckpid = pidh.read()
            if os.getpid() == int(lckpid):
                os.remove(__PIDFILE__)

    @staticmethod
    def synccount_increase():
        SyncY.EXLock.acquire()
        SyncY.synccount += 1
        SyncY.EXLock.release()

    @staticmethod
    def errorcount_increase():
        SyncY.EXLock.acquire()
        SyncY.errorcount += 1
        SyncY.EXLock.release()

    @staticmethod
    def failcount_increase():
        SyncY.EXLock.acquire()
        SyncY.failcount += 1
        SyncY.EXLock.release()

    @staticmethod
    def reset_counter():
        SyncY.EXLock.acquire()
        SyncY.synccount = 0
        SyncY.failcount = 0
        SyncY.errorcount = 0
        SyncY.EXLock.release()

    @staticmethod
    def __init_syncdata():
        SyncY.syncData = {}
        if os.path.exists(SyncY.syncydb):
            with open(SyncY.syncydb, 'rb') as sydb:
                flock(sydb, LOCK_SH)
                sydb.seek(64)
                datarec = sydb.read(64)
                while datarec:
                    SyncY.syncData[datarec[0:16]] = datarec[16:]
                    datarec = sydb.read(64)
                flock(sydb, LOCK_UN)

    def __check_expires(self):
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://openapi.baidu.com/rest/2.0/passport/users/getLoggedInUser', {}, {'access_token': SyncY.syncytoken['access_token']}, 'POST', SYCurl.Normal)
        responses = json.loads(responses)
        if 'uid' in responses:
            retcode, responses = sycurl.request('https://www.syncy.cn/syserver', {}, {'method': 'get_last_version', 'edition': 'python', 'ver': __VERSION__, 'uid': responses['uid'], 'code': SyncY.syncytoken['device_code']}, 'POST', SYCurl.Normal)
            if retcode == 200 and responses.find('#') > -1:
                (lastver, smessage) = responses.strip('\n').split('#', 1)
                if lastver > __VERSION__:
                    printlog('%s WARNING: %s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), smessage.encode(__CHARSET__)))
        if (SyncY.syncytoken['refresh_date'] + SyncY.syncytoken['expires_in'] - 864000) > int(time.time()):
            return
        retcode, retbody = sycurl.request('https://www.syncy.cn/syserver', {}, {'method': 'refresh_access_token', 'refresh_token': SyncY.syncytoken['refresh_token'], 'code': SyncY.syncytoken['device_code']}, 'POST', SYCurl.Normal)
        responses = json.loads(retbody)
        try:
            if retcode != 200:
                printlog('%s ERROR(Errno:%d): Refresh access token failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, responses['error_msg']))
                return 1
            SyncY.syncytoken['refresh_token'] = responses['refresh_token']
            SyncY.syncytoken['access_token'] = responses['access_token']
            SyncY.syncytoken['expires_in'] = int(responses['expires_in'])
            SyncY.syncytoken['refresh_date'] = int(time.time())
        except KeyError:
            printlog('%s ERROR(Errno:%d): Refresh access token failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, retbody))
            return 1
        self.__save_config()
        printlog('%s INFO: Refresh access token success.' % time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()))
        return 0

    @staticmethod
    def __save_config():
        with open('%s.tmp' % __CONFIG_FILE__, 'w') as sycfg:
            sycfg.write("\nconfig syncy\n")
            for key, value in SyncY.config.items():
                sycfg.write("\toption %s '%s'\n" % (key, str(value)))
            sycfg.write("\nconfig syncytoken\n")
            for key, value in SyncY.syncytoken.items():
                sycfg.write("\toption %s '%s'\n" % (key, str(value)))
            for i in range(len(SyncY.syncpath)):
                sycfg.write("\nconfig syncpath\n")
                for key, value in SyncY.syncpath[str(i)].items():
                    sycfg.write("\toption %s '%s'\n" % (key, str(value)))
            sycfg.flush()
            os.fsync(sycfg.fileno())
        if os.path.exists('%s.tmp' % __CONFIG_FILE__):
            pmeta = os.stat(__CONFIG_FILE__)
            rename('%s.tmp' % __CONFIG_FILE__, __CONFIG_FILE__)
            if os.name == 'posix':
                os.lchown(__CONFIG_FILE__, pmeta.st_uid, pmeta.st_gid)
                os.chmod(__CONFIG_FILE__, pmeta.st_mode)

    @staticmethod
    def __catpath(*names):
        fullpath = '/'.join(names)
        fullpath = re.sub(r'/+', '/', fullpath)
        fullpath = re.sub(r'/$', '', fullpath)
        return fullpath

    @staticmethod
    def __get_newname(oldname):
        nowtime = str(time.strftime("%Y%m%d%H%M%S", time.localtime()))
        m = SyncY.syre['newname'].findall(oldname)
        if m:
            newname = m[0][0] + '_old_' + nowtime + m[0][1]
        else:
            newname = oldname + '_old_' + nowtime
        return newname

    @staticmethod
    def __check_pcspath(pcsdirname, pcsfilename):
        if len(pcsdirname) + len(pcsfilename) + 1 >= 1000:
            printlog('%s ERROR: Length of PCS path(%s/%s) must less than 1000, skip upload.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), pcsdirname, pcsfilename))
            return 1
        if SyncY.syre['pcspath'].findall(pcsfilename):
            printlog('%s ERROR: PCS path(%s/%s) is invalid, please check whether special characters exists in the path, skip upload the file.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), pcsdirname, pcsfilename))
            return 1
        return 0

    @staticmethod
    def __get_pcs_quota():
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/quota', {'method': 'info', 'access_token': SyncY.syncytoken['access_token']}, '', 'GET', SYCurl.Normal)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            printlog('%s ERROR(Errno:%d): Get pcs quota failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, responses['error_msg']))
            return 1
        printlog('%s INFO: PCS quota is %dG,used %dG.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), responses['quota'] / 1024 / 1024 / 1024, responses['used'] / 1024 / 1024 / 1024))
        return 0

    @staticmethod
    def __get_pcs_filelist(pcspath, startindex, endindex):
        if __DEBUG__:
            printlog('%s Info(%s): Start get pcs file list(%d-%d) of "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, startindex, endindex, pcspath))
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'list', 'access_token': SyncY.syncytoken['access_token'], 'path': pcspath, 'limit': '%d-%d' % (startindex, endindex), 'by': 'name', 'order': 'asc'}, '', 'GET', SYCurl.Normal)
        try:
            responses = json.loads(responses)
            if retcode != 200 or 'error_code' in responses:
                if responses['error_code'] == 31066:
                    return 31066, []
                else:
                    printlog('%s ERROR(Errno:%d): Get PCS file list of "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, pcspath, responses['error_msg']))
                    return 1, []
            return 0, responses['list']
        except Exception, e:
            printlog('%s ERROR: Get PCS file list of "%s" failed. return code: %d, response body: %s.\n%s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), pcspath, retcode, str(responses), e, traceback.format_exc()))
            return 1, []
        finally:
            del responses
            if __DEBUG__:
                printlog('%s Info(%s): Complete get pcs file list(%d-%d) of "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, startindex, endindex, pcspath))

    @staticmethod
    def __check_create_pcsdir(pcspath):
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'meta', 'access_token': SyncY.syncytoken['access_token'], 'path': pcspath}, '', 'GET', SYCurl.Normal)
        try:
            responses = json.loads(responses)
            if retcode == 200 and responses['list'][0]['isdir'] == 1:
                return 0
            elif (retcode != 200 and responses['error_code'] == 31066) or (retcode == 200 and responses['list'][0]['isdir'] == 0):
                retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'mkdir', 'access_token': SyncY.syncytoken['access_token'], 'path': pcspath}, '', 'POST', SYCurl.Normal)
                responses = json.loads(responses)
                if retcode == 200 and responses['path'].encode(__CHARSET__) == pcspath:
                    return 0
            printlog('%s ERROR(Errno:%d): Create PCS directory "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, pcspath, responses['error_msg']))
            return 1
        except Exception, e:
            printlog('%s ERROR: Create PCS directory "%s" failed. return code: %d, response body: %s.\n%s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), pcspath, retcode, str(responses), e, traceback.format_exc()))
            return 1

    def __rm_localfile(self, delpath, slient=False):
        try:
            if os.path.isfile(delpath):
                os.remove(delpath)
                if not slient:
                    printlog('%s INFO: Delete local file "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), delpath))
            elif os.path.isdir(delpath):
                fnlist = os.listdir(delpath)
                for i in xrange(len(fnlist)):
                    self.__rm_localfile('%s/%s' % (delpath, fnlist[i]), slient)
                os.rmdir(delpath)
                if not slient:
                    printlog('%s INFO: Delete local directory "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), delpath))
        except Exception, e:
            if not slient:
                printlog('%s ERROR: Delete local file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), delpath, e, traceback.format_exc()))
            return 1
        return 0

    @staticmethod
    def __rm_pcsfile(pcspath, slient=False):
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'delete', 'access_token': SyncY.syncytoken['access_token'], 'path': pcspath}, '', 'POST', SYCurl.Normal)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            if not slient:
                printlog('%s ERROR(Errno:%d): Delete remote file or directory "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, pcspath, responses['error_msg']))
            return 1
        if not slient:
            printlog('%s INFO: Delete remote file or directory "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), pcspath))
        return 0

    @staticmethod
    def __mv_pcsfile(oldpcspath, newpcspath, slient=False):
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'move', 'access_token': SyncY.syncytoken['access_token'], 'from': oldpcspath, 'to': newpcspath}, '', 'POST', SYCurl.Normal)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            if not slient:
                printlog('%s ERROR(Errno:%d): Move remote file or directory "%s" to "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, oldpcspath, newpcspath, responses['error_msg']))
            return 1
        if not slient:
            printlog('%s INFO: Move remote file or directory "%s" to "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), oldpcspath, newpcspath))
        return 0

    @staticmethod
    def __cp_pcsfile(srcpcspath, destpcspath):
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'copy', 'access_token': SyncY.syncytoken['access_token'], 'from': srcpcspath, 'to': destpcspath}, '', 'POST', SYCurl.Normal)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            printlog('%s ERROR(Errno:%d): Copy remote file or directory "%s" to "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, srcpcspath, destpcspath, responses['error_msg']))
            return 1
        printlog('%s INFO: Copy remote file or directory "%s" to "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), srcpcspath, destpcspath))
        return 0

    @staticmethod
    def __get_pcs_filemeta(pcspath):
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'meta', 'access_token': SyncY.syncytoken['access_token'], 'path': pcspath}, '', 'GET', SYCurl.Normal)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            printlog('%s ERROR(Errno:%d): Get file\'s meta failed: %s, %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, pcspath, responses['error_msg']))
            return 1, {}
        return 0, responses['list'][0]

    @staticmethod
    def __upload_file_nosync(filepath, pcspath):
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://c.pcs.baidu.com/rest/2.0/pcs/file', {'method': 'upload', 'access_token': SyncY.syncytoken['access_token'], 'path': pcspath, 'ondup': 'newcopy'}, '0-%d' % (os.stat(filepath).st_size - 1), 'POST', SYCurl.Upload, filepath)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            printlog('%s ERROR(Errno:%d): Upload file to pcs failed: %s, %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, filepath, responses['error_msg']))
            return 1
        printlog('%s INFO: Upload file "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), filepath))
        return 0

    def __compress_data(self, pathname, sydbnew, sydb=None, sydblen=0):
        fnlist = os.listdir(pathname)
        fnlist.sort()
        for fnname in fnlist:
            if fnname[0:1] == '.':
                continue
            fullpath = '%s/%s' % (pathname, fnname)
            if os.path.isdir(fullpath):
                if SyncY.config['datacache'] == 'on':
                    self.__compress_data(fullpath, sydbnew)
                else:
                    self.__compress_data(fullpath, sydbnew, sydb, sydblen)
            elif os.path.isfile(fullpath):
                fnmd5 = hashlib.md5(fullpath[SyncY.basedirlen:]).digest()
                fnstat = os.stat(fullpath)
                fmate = struct.pack('>qq', int(fnstat.st_mtime), fnstat.st_size)
                if SyncY.config['datacache'] == 'on':
                    if fnmd5 in SyncY.syncData and SyncY.syncData[fnmd5][0:16] == fmate:
                        sydbnew.write('%s%s' % (fnmd5, SyncY.syncData[fnmd5]))
                        del SyncY.syncData[fnmd5]
                else:
                    if sydb.tell() == sydblen:
                        sydb.seek(64)
                    datarec = sydb.read(64)
                    readlen = 64
                    while datarec and readlen <= sydblen - 64:
                        if datarec[0:32] == '%s%s' % (fnmd5, fmate):
                            sydbnew.write(datarec)
                            break
                        if readlen == sydblen - 64:
                            break
                        if sydb.tell() == sydblen:
                            sydb.seek(64)
                        datarec = sydb.read(64)
                        readlen += 64
        return 0

    def __start_compress(self, pathname=''):
        if pathname == '':
            mpath = []
            for i in range(len(SyncY.syncpath)):
                if SyncY.syncpath[str(i)]['synctype'].lower() not in ['4', 's', 'sync']:
                    mpath.append(SyncY.syncpath[str(i)]['localpath'])
            printlog('%s INFO: Start compress sync data.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))
        else:
            mpath = [pathname]
        for ipath in mpath:
            if ipath == '':
                continue
            SyncY.basedirlen = len(ipath)
            SyncY.syncydb = '%s/.syncy.info.db' % ipath
            newdbfile = '%s/.syncy.info.dbtmp' % ipath
            if os.path.exists(SyncY.syncydb):
                if os.path.exists(newdbfile):
                    os.remove(newdbfile)
                self.__check_upgrade_syncdata(newdbfile)
                with open(newdbfile, 'ab') as sydbnew:
                    if SyncY.config['datacache'] == 'on':
                        self.__init_syncdata()
                        self.__compress_data(ipath, sydbnew)
                        SyncY.syncData = None
                    else:
                        sydblen = os.stat(SyncY.syncydb).st_size
                        with open(SyncY.syncydb, 'rb') as sydb:
                            self.__compress_data(ipath, sydbnew, sydb, sydblen)
                    sydbnew.flush()
                    os.fsync(sydbnew.fileno())
                rename(newdbfile, SyncY.syncydb)
        if pathname == '':
            SyncY.syncytoken['compress_date'] = int(time.time())
            SyncY.syncytoken['synctotal'] = 0
            self.__save_config()
            printlog('%s INFO: Sync data compress completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))

    def __check_excludefiles(self, filepath):
        for reexf in self._excludefiles:
            if reexf.findall(filepath):
                return 1
        return 0

    @staticmethod
    def __check_syncstatus(fmd5, fmate, rmate, rmd5):
        if rmd5 != '*':
            rmd5 = rmd5.decode('hex')
        if SyncY.config['datacache'] == 'on':
            if fmd5 not in SyncY.syncData:
                return 0
            if rmd5 == '*' and rmate == '*' and SyncY.syncData[fmd5][0:16] == fmate:
                return 1
            elif fmate == '*' and SyncY.syncData[fmd5][16:] == rmate + rmd5:
                return 1
            elif SyncY.syncData[fmd5] == fmate + rmate + rmd5:
                return 1
        else:
            if SyncY.sydb.tell() == SyncY.sydblen:
                SyncY.sydb.seek(64)
            datarec = SyncY.sydb.read(64)
            readlen = 64
            while datarec and readlen <= SyncY.sydblen - 64:
                if rmd5 == '*' and rmate == '*' and datarec[0:32] == fmd5 + fmate:
                    return 1
                elif fmate == '*' and datarec[16:] == rmate + rmd5:
                    return 1
                elif datarec == fmd5 + fmate + rmate + rmd5:
                    return 1
                if readlen == SyncY.sydblen - 64:
                    break
                if SyncY.sydb.tell() == SyncY.sydblen:
                    SyncY.sydb.seek(64)
                datarec = SyncY.sydb.read(64)
                readlen += 64
        return 0

    def __syncy_upload(self, ldir, rdir):
        fnlist = os.listdir(ldir)
        fnlist.sort()
        for fi in xrange(len(fnlist)):
            lfullpath = '%s/%s' % (ldir, fnlist[fi])
            fmtime = 0
            fsize = 0
            try:
                if fnlist[fi][0:1] == '.' or self.__check_excludefiles(lfullpath) == 1 or self.__check_pcspath(rdir, fnlist[fi]) == 1:
                    continue
                if __DEBUG__:
                    printlog('%s Info(%s): Start upload "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, lfullpath))
                rfullpath = '%s/%s' % (rdir, fnlist[fi])
                if os.path.isdir(lfullpath):
                    self.__syncy_upload(lfullpath, rfullpath)
                else:
                    fmeta = os.stat(lfullpath)
                    fmtime = int(fmeta.st_mtime)
                    fsize = fmeta.st_size
                    fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                    if self.__check_syncstatus(fnmd5, struct.pack('>qq', fmtime, fsize), '*', '*') == 0:
                        if SyncY.config['ondup'] == 'rename':
                            ondup = 'newcopy'
                        else:
                            ondup = 'overwrite'
                        if SyncY.TaskSemaphore.acquire():
                            synctask = SYTask(SYTask.Upload, lfullpath, int(fmeta.st_mtime), fmeta.st_size, fnmd5, rfullpath, 0, 0, '', ondup)
                            synctask.start()
                    else:
                        continue
            except struct.error, e:
                printlog('%s ERROR: Struct.pack upload file mate(mtime:%d,size:%d) of "%s" error: %s\n%s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), fmtime, fsize, lfullpath, e, traceback.format_exc()))
                self.errorcount_increase()
                return 1
            except Exception, e:
                printlog('%s ERROR: Upload file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                self.errorcount_increase()
                return 1
        return 0

    def __syncy_uploadplus(self, ldir, rdir):
        startidx = 0
        retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, SyncY.config['listnumber'])
        if retcode != 0 and retcode != 31066:
            self.errorcount_increase()
            return 1
        lfnlist = os.listdir(ldir)
        lfnlist.sort()
        while retcode == 0:
            for i in xrange(len(rfnlist)):
                rfullpath = rfnlist[i]['path'].encode(__CHARSET__)
                fnname = os.path.basename(rfullpath)
                lfullpath = '%s/%s' % (ldir, fnname)
                try:
                    if self.__check_excludefiles(lfullpath) == 1:
                        continue
                    if os.path.exists(lfullpath):
                        for idx in xrange(len(lfnlist)):
                            if lfnlist[idx] == fnname:
                                del lfnlist[idx]
                                break
                    else:
                        continue
                    if __DEBUG__:
                        printlog('%s Info(%s): Start upload+ "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, lfullpath))
                    if (rfnlist[i]['isdir'] == 1 and os.path.isfile(lfullpath)) or (rfnlist[i]['isdir'] == 0 and os.path.isdir(lfullpath)):
                        if SyncY.config['ondup'] == 'rename':
                            fnnamenew = '%s/%s' % (rdir, self.__get_newname(fnname))
                            if len(fnnamenew) >= 1000:
                                printlog('%s ERROR: Rename failed, the length of PCS path "%s" must less than 1000, skip upload "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), fnnamenew, lfullpath))
                                self.failcount_increase()
                                continue
                            if self.__mv_pcsfile(rfullpath, fnnamenew, True) == 1:
                                printlog('%s ERROR: Rename "%s" failed, skip upload "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), rfullpath, lfullpath))
                                self.errorcount_increase()
                                continue
                        else:
                            self.__rm_pcsfile(rfullpath, True)
                        if os.path.isdir(lfullpath):
                            self.__syncy_uploadplus(lfullpath, rfullpath)
                            continue
                        else:
                            fmeta = os.stat(lfullpath)
                            fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                            if SyncY.TaskSemaphore.acquire():
                                synctask = SYTask(SYTask.Upload, lfullpath, int(fmeta.st_mtime), fmeta.st_size, fnmd5, rfullpath, 0, 0, '', 'overwrite')
                                synctask.start()
                    elif rfnlist[i]['isdir'] == 1:
                        self.__syncy_uploadplus(lfullpath, rfullpath)
                        continue
                    else:
                        fmeta = os.stat(lfullpath)
                        fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                        if self.__check_syncstatus(fnmd5, struct.pack('>qq', int(fmeta.st_mtime), fmeta.st_size), struct.pack('>qq', rfnlist[i]['mtime'], rfnlist[i]['size']), rfnlist[i]['md5']) == 1:
                            continue
                        if SyncY.config['ondup'] == 'rename':
                            fnnamenew = '%s/%s' % (rdir, self.__get_newname(fnname))
                            if len(fnnamenew) >= 1000:
                                printlog('%s ERROR: Rename failed, the length of PCS path "%s" must less than 1000, skip upload "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), fnnamenew, lfullpath))
                                self.failcount_increase()
                                continue
                            if self.__mv_pcsfile(rfullpath, fnnamenew, True) == 1:
                                printlog('%s ERROR: Rename "%s" failed, skip upload "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), rfullpath, lfullpath))
                                self.failcount_increase()
                                continue
                        else:
                            self.__rm_pcsfile(rfullpath, True)
                        if SyncY.TaskSemaphore.acquire():
                            synctask = SYTask(SYTask.Upload, lfullpath, int(fmeta.st_mtime), fmeta.st_size, fnmd5, rfullpath, 0, 0, '', 'overwrite')
                            synctask.start()
                except struct.error, e:
                    printlog('%s ERROR: Struct.pack file mate of "%s" error: %s\n%s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 1
                except Exception, e:
                    printlog('%s ERROR: Upload file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 1
            if len(rfnlist) < SyncY.config['listnumber']:
                break
            startidx += SyncY.config['listnumber']
            retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, startidx + SyncY.config['listnumber'])
            if retcode != 0:
                self.errorcount_increase()
                return 1
        for idx in xrange(len(lfnlist)):
            lfullpath = '%s/%s' % (ldir, lfnlist[idx])
            try:
                if lfnlist[idx][0:1] == '.' or self.__check_excludefiles(lfullpath) == 1 or self.__check_pcspath(rdir, lfnlist[idx]) == 1:
                    continue
                rfullpath = '%s/%s' % (rdir, lfnlist[idx])
                if os.path.isdir(lfullpath):
                    self.__syncy_uploadplus(lfullpath, rfullpath)
                elif os.path.isfile(lfullpath):
                    fmeta = os.stat(lfullpath)
                    fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                    if SyncY.TaskSemaphore.acquire():
                        synctask = SYTask(SYTask.Upload, lfullpath, int(fmeta.st_mtime), fmeta.st_size, fnmd5, rfullpath, 0, 0, '', 'overwrite')
                        synctask.start()
            except struct.error, e:
                printlog('%s ERROR: Struct.pack file mate of "%s" error: %s\n%s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                self.errorcount_increase()
                return 1
            except Exception, e:
                printlog('%s ERROR: Upload file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                self.errorcount_increase()
                return 1
        return 0

    def __syncy_download(self, ldir, rdir):
        startidx = 0
        retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, SyncY.config['listnumber'])
        if retcode != 0:
            self.errorcount_increase()
            return 1
        while retcode == 0:
            for i in xrange(len(rfnlist)):
                rfullpath = rfnlist[i]['path'].encode(__CHARSET__)
                fnname = os.path.basename(rfullpath)
                if self.__check_excludefiles(rfullpath) == 1:
                    continue
                lfullpath = '%s/%s' % (ldir, fnname)
                try:
                    if __DEBUG__:
                        printlog('%s Info(%s): Start download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, rfullpath))
                    if rfnlist[i]['isdir'] == 1:
                        if os.path.exists(lfullpath) and os.path.isfile(lfullpath):
                            if SyncY.config['ondup'] == 'rename':
                                fnnamenew = '%s/%s' % (ldir, self.__get_newname(fnname))
                                rename(lfullpath, fnnamenew)
                            else:
                                if self.__rm_localfile(lfullpath, True) == 1:
                                    printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                    self.errorcount_increase()
                                    continue
                        if not (os.path.exists(lfullpath)):
                            os.mkdir(lfullpath)
                            if os.name == 'posix':
                                pmeta = os.stat(ldir)
                                os.lchown(lfullpath, pmeta.st_uid, pmeta.st_gid)
                                os.chmod(lfullpath, pmeta.st_mode)
                        self.__syncy_download(lfullpath, rfullpath)
                    else:
                        fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                        if not (os.path.exists(lfullpath + '.db.syy')):
                            if self.__check_syncstatus(fnmd5, '*', struct.pack('>qq', rfnlist[i]['mtime'], rfnlist[i]['size']), rfnlist[i]['md5']) == 1:
                                continue
                            if os.path.exists(lfullpath) and SyncY.config['ondup'] == 'rename':
                                fnnamenew = '%s/%s' % (ldir, self.__get_newname(fnname))
                                rename(lfullpath, fnnamenew)
                            elif os.path.exists(lfullpath):
                                if self.__rm_localfile(lfullpath, True) == 1:
                                    printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                    self.failcount_increase()
                                    continue
                        if SyncY.TaskSemaphore.acquire():
                            synctask = SYTask(SYTask.Download, lfullpath, 0, 0, fnmd5, rfullpath, rfnlist[i]['mtime'], rfnlist[i]['size'], rfnlist[i]['md5'], 'overwrite')
                            synctask.start()
                except struct.error, e:
                    printlog('%s ERROR: Struct.pack file mate of "%s" error: %s\n%s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 1
                except Exception, e:
                    printlog('%s ERROR: Download file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 1
            if len(rfnlist) < SyncY.config['listnumber']:
                break
            startidx += SyncY.config['listnumber']
            retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, startidx + SyncY.config['listnumber'])
            if retcode != 0:
                self.errorcount_increase()
                return 1
        return 0

    def __syncy_downloadplus(self, ldir, rdir):
        startidx = 0
        retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, SyncY.config['listnumber'])
        if retcode != 0:
            self.errorcount_increase()
            return 1
        while retcode == 0:
            for i in xrange(0, len(rfnlist), 1):
                rfullpath = rfnlist[i]['path'].encode(__CHARSET__)
                fnname = os.path.basename(rfullpath)
                if self.__check_excludefiles(rfullpath) == 1:
                    continue
                lfullpath = '%s/%s' % (ldir, fnname)
                try:
                    if __DEBUG__:
                        printlog('%s Info(%s): Start download+ "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, rfullpath))
                    if rfnlist[i]['isdir'] == 1:
                        if os.path.exists(lfullpath) and os.path.isfile(lfullpath):
                            if SyncY.config['ondup'] == 'rename':
                                fnnamenew = '%s/%s' % (ldir, self.__get_newname(fnname))
                                rename(lfullpath, fnnamenew)
                            else:
                                if self.__rm_localfile(lfullpath, True) == 1:
                                    printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                    self.errorcount_increase()
                                    continue
                        if not (os.path.exists(lfullpath)):
                            os.mkdir(lfullpath)
                            if os.name == 'posix':
                                pmeta = os.stat(ldir)
                                os.lchown(lfullpath, pmeta.st_uid, pmeta.st_gid)
                                os.chmod(lfullpath, pmeta.st_mode)
                        self.__syncy_downloadplus(lfullpath, rfullpath)
                    else:
                        fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                        if os.path.exists(lfullpath) and not (os.path.exists(lfullpath + '.db.syy')):
                            fmeta = os.stat(lfullpath)
                            if self.__check_syncstatus(fnmd5, struct.pack('>qq', int(fmeta.st_mtime), fmeta.st_size), struct.pack('>qq', rfnlist[i]['mtime'], rfnlist[i]['size']), rfnlist[i]['md5']) == 1:
                                continue
                            if SyncY.config['ondup'] == 'rename':
                                fnnamenew = '%s/%s' % (ldir, self.__get_newname(fnname))
                                rename(lfullpath, fnnamenew)
                            else:
                                if self.__rm_localfile(lfullpath, True) == 1:
                                    printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                    self.failcount_increase()
                                    continue
                        if SyncY.TaskSemaphore.acquire():
                            synctask = SYTask(SYTask.Download, lfullpath, 0, 0, fnmd5, rfullpath, rfnlist[i]['mtime'], rfnlist[i]['size'], rfnlist[i]['md5'], 'overwrite')
                            synctask.start()
                except struct.error, e:
                    printlog('%s ERROR: Struct.pack file mate of "%s" error: %s\n%s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 1
                except Exception, e:
                    printlog('%s ERROR: Download file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 1
            if len(rfnlist) < SyncY.config['listnumber']:
                break
            startidx += SyncY.config['listnumber']
            retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, startidx + SyncY.config['listnumber'])
            if retcode != 0:
                self.errorcount_increase()
                return 1
        return 0

    def __syncy_sync(self, ldir, rdir):
        del_rfiles = 0
        del_lfiles = 0
        startidx = 0
        retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, SyncY.config['listnumber'])
        if retcode != 0 and retcode != 31066:
            self.errorcount_increase()
            return 0, 0
        lfnlist = os.listdir(ldir)
        lfnlist.sort()
        while retcode == 0:
            for i in xrange(len(rfnlist)):
                rfullpath = rfnlist[i]['path'].encode(__CHARSET__)
                fnname = os.path.basename(rfullpath)
                if self.__check_excludefiles(rfullpath) == 1:
                    continue
                lfullpath = '%s/%s' % (ldir, fnname)
                try:
                    if os.path.exists(lfullpath):
                        for idx in xrange(len(lfnlist)):
                            if lfnlist[idx] == fnname:
                                del lfnlist[idx]
                                break
                    if __DEBUG__:
                        printlog('%s Info(%s): Start sync "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, lfullpath))
                    if rfnlist[i]['isdir'] == 1:
                        if os.path.exists(lfullpath) and os.path.isfile(lfullpath):
                            fmeta = os.stat(lfullpath)
                            fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                            if self.__check_syncstatus(fnmd5, struct.pack('>qq', int(fmeta.st_mtime), fmeta.st_size), '*', '*') == 1 or rfnlist[i]['mtime'] > int(fmeta.st_mtime):
                                if self.__rm_localfile(lfullpath, True) == 1:
                                    printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                    self.failcount_increase()
                                    continue
                                self.__syncy_downloadplus(lfullpath, rfullpath)
                                continue
                            else:
                                self.__rm_pcsfile(rfullpath, True)
                                if SyncY.TaskSemaphore.acquire():
                                    synctask = SYTask(SYTask.Upload, lfullpath, int(fmeta.st_mtime), fmeta.st_size, fnmd5, rfullpath, 0, 0, '', 'overwrite')
                                    synctask.start()
                        elif not (os.path.exists(lfullpath)):
                            os.mkdir(lfullpath)
                            if os.name == 'posix':
                                pmeta = os.stat(ldir)
                                os.lchown(lfullpath, pmeta.st_uid, pmeta.st_gid)
                                os.chmod(lfullpath, pmeta.st_mode)
                            l_del, r_del = self.__syncy_sync(lfullpath, rfullpath)
                            if r_del > 0 and self.__get_pcs_filelist(rfullpath, 0, 10) == (0, []):
                                os.rmdir(lfullpath)
                                self.__rm_pcsfile(rfullpath, True)
                                del_rfiles += 1
                            continue
                        else:
                            self.__syncy_sync(lfullpath, rfullpath)
                            continue
                    else:
                        fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                        fmtime = 0
                        fsize = 0
                        rmkey = struct.pack('>qq', rfnlist[i]['mtime'], rfnlist[i]['size'])
                        if os.path.exists(lfullpath) and os.path.isdir(lfullpath):
                            if self.__check_syncstatus(fnmd5, '*', rmkey, rfnlist[i]['md5']) == 1:
                                if self.__rm_pcsfile(rfullpath, True) == 1:
                                    printlog('%s ERROR: Delete remote file "%s" failed, skip sync "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), rfullpath, lfullpath))
                                    self.errorcount_increase()
                                    continue
                                self.__syncy_uploadplus(lfullpath, rfullpath)
                                continue
                            else:
                                if rfnlist[i]['mtime'] > int(os.stat(lfullpath).st_mtime):
                                    if self.__rm_localfile(lfullpath, True) == 1:
                                        printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                        self.failcount_increase()
                                        continue
                                    sync_op = SYTask.Download
                                else:
                                    if self.__rm_pcsfile(rfullpath, True) == 1:
                                        printlog('%s ERROR: Delete remote file "%s" failed, skip sync "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), rfullpath, lfullpath))
                                        self.errorcount_increase()
                                        continue
                                    self.__syncy_uploadplus(lfullpath, rfullpath)
                                    continue
                        elif os.path.exists(lfullpath):
                            fmeta = os.stat(lfullpath)
                            fmtime = int(fmeta.st_mtime)
                            fsize = fmeta.st_size
                            fmkey = struct.pack('>qq', fmtime, fsize)
                            if self.__check_syncstatus(fnmd5, fmkey, rmkey, rfnlist[i]['md5']) == 1:
                                continue
                            elif self.__check_syncstatus(fnmd5, fmkey, '*', '*') == 1:
                                if self.__rm_localfile(lfullpath, True) == 1:
                                    printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                    self.failcount_increase()
                                    continue
                                sync_op = SYTask.Download
                            elif self.__check_syncstatus(fnmd5, '*', rmkey, rfnlist[i]['md5']) == 1:
                                self.__rm_pcsfile(rfullpath, True)
                                sync_op = SYTask.Upload
                            elif os.path.exists('%s.db.syy' % lfullpath):
                                with open('%s.db.syy' % lfullpath, 'r') as infoh:
                                    syyinfo = infoh.readline()
                                if syyinfo.strip('\n') == 'download:%s:%d' % (rfnlist[i]['md5'], rfnlist[i]['size']):
                                    sync_op = SYTask.Download
                                else:
                                    os.remove('%s.db.syy' % lfullpath)
                                    if rfnlist[i]['mtime'] > fmtime:
                                        if self.__rm_localfile(lfullpath, True) == 1:
                                            printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                            self.failcount_increase()
                                            continue
                                        sync_op = SYTask.Download
                                    else:
                                        self.__rm_pcsfile(rfullpath, True)
                                        sync_op = SYTask.Upload
                            elif rfnlist[i]['mtime'] > fmtime:
                                if self.__rm_localfile(lfullpath, True) == 1:
                                    printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                                    self.failcount_increase()
                                    continue
                                sync_op = SYTask.Download
                            else:
                                self.__rm_pcsfile(rfullpath)
                                rfnlist[i]['mtime'] = 0
                                sync_op = SYTask.Upload
                        else:
                            if self.__check_syncstatus(fnmd5, '*', rmkey, rfnlist[i]['md5']) == 1:
                                if self.__rm_pcsfile(rfullpath) == 1:
                                    self.failcount_increase()
                                else:
                                    self.synccount_increase()
                                del_rfiles += 1
                                continue
                            else:
                                sync_op = SYTask.Download
                        if SyncY.TaskSemaphore.acquire():
                            synctask = SYTask(sync_op, lfullpath, fmtime, fsize, fnmd5, rfullpath, rfnlist[i]['mtime'], rfnlist[i]['size'], rfnlist[i]['md5'], 'overwrite')
                            synctask.start()
                except struct.error, e:
                    printlog('%s ERROR: Struct.pack file mate of "%s" error: %s\n%s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 0, 0
                except Exception, e:
                    printlog('%s ERROR: Sync file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                    self.errorcount_increase()
                    return 0, 0
            if len(rfnlist) < SyncY.config['listnumber']:
                break
            startidx += SyncY.config['listnumber']
            retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, startidx + SyncY.config['listnumber'])
            if retcode != 0:
                self.errorcount_increase()
                return 0, 0
        for idx in xrange(len(lfnlist)):
            lfullpath = '%s/%s' % (ldir, lfnlist[idx])
            if lfnlist[idx][0:1] == '.' or self.__check_excludefiles(lfullpath) == 1 or self.__check_pcspath(rdir, lfnlist[idx]) == 1:
                continue
            rfullpath = '%s/%s' % (rdir, lfnlist[idx])
            try:
                if __DEBUG__:
                    printlog('%s Info(%s): Start sync(upload) "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, lfullpath))
                if os.path.isdir(lfullpath):
                    if len(os.listdir(lfullpath)) == 0:
                        if self.__check_create_pcsdir(rfullpath) != 0:
                            self.failcount_increase()
                    else:
                        l_del, r_del = self.__syncy_sync(lfullpath, rfullpath)
                        if l_del > 0 and len(os.listdir(lfullpath)) == 0:
                            os.rmdir(lfullpath)
                            del_lfiles += 1
                elif os.path.isfile(lfullpath):
                    fmeta = os.stat(lfullpath)
                    fmtime = int(fmeta.st_mtime)
                    fsize = fmeta.st_size
                    fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                    if self.__check_syncstatus(fnmd5, struct.pack('>qq', fmtime, fsize), '*', '*') == 1:
                        if self.__rm_localfile(lfullpath, True) == 1:
                            printlog('%s ERROR: Delete local file "%s" failed, skip download "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, rfullpath))
                            self.failcount_increase()
                        else:
                            self.synccount_increase()
                        del_lfiles += 1
                        continue
                    elif os.path.exists('%s.db.syy' % lfullpath):
                        with open('%s.db.syy' % lfullpath, 'r') as infoh:
                            syyinfo = infoh.readline()
                        if syyinfo.strip('\n') != 'upload:%d:%d' % (fmtime, fsize):
                            if syyinfo[0:6] == 'upload':
                                os.remove('%s.db.syy' % lfullpath)
                            else:
                                os.remove(lfullpath)
                                os.remove('%s.db.syy' % lfullpath)
                                continue
                    if SyncY.TaskSemaphore.acquire():
                        synctask = SYTask(SYTask.Upload, lfullpath, fmtime, fsize, fnmd5, rfullpath, 0, 0, '', 'overwrite')
                        synctask.start()
            except struct.error, e:
                printlog('%s ERROR: Struct.pack file mate of "%s" error: %s\n%s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                self.errorcount_increase()
                return 0, 0
            except Exception, e:
                printlog('%s ERROR: Sync file "%s" failed. %s\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), lfullpath, e, traceback.format_exc()))
                self.errorcount_increase()
                return 0, 0
        return del_lfiles, del_rfiles

    def __start_sync(self):
        self.__get_pcs_quota()
        for i in range(len(SyncY.syncpath)):
            if 'localpath' not in SyncY.syncpath[str(i)] or 'remotepath' not in SyncY.syncpath[str(i)] or 'synctype' not in SyncY.syncpath[str(i)] or 'enable' not in SyncY.syncpath[str(i)]:
                printlog('%s ERROR: The %d\'s of syncpath setting is invalid.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), i + 1))
                continue
            if SyncY.syncpath[str(i)]['enable'] == '0':
                continue
            self.reset_counter()
            localpath = self.__catpath(SyncY.syncpath[str(i)]['localpath'])
            remotepath = self.__catpath(SyncY.pcsroot, SyncY.syncpath[str(i)]['remotepath'])
            ipath = ('%s:%s:%s' % (localpath, remotepath, SyncY.syncpath[str(i)]['synctype']))
            printlog('%s INFO: Start sync path "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
            ckdir = 0
            for rdir in remotepath.split('/'):
                if re.findall(r'^[\s\.\n].*|.*[/<>\\|\*\?:\"].*|.*[\s\.\n]$', rdir):
                    ckdir = 1
                    break
            if ckdir != 0:
                printlog('%s ERROR: Sync "%s" failed, remote directory error.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                continue
            if not (os.path.exists(localpath)):
                os.mkdir(localpath)
                if os.name == 'posix':
                    pmeta = os.stat(os.path.dirname(localpath))
                    os.lchown(localpath, pmeta.st_uid, pmeta.st_gid)
                    os.chmod(localpath, pmeta.st_mode)
            if self.__check_create_pcsdir(remotepath) != 0:
                printlog('%s ERROR Sync path: "%s" failed, remote directory is not exists and create directory failed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                continue
            if localpath != '' and os.path.isdir(localpath):
                SyncY.syncydb = '%s/.syncy.info.db' % localpath
                SyncY.basedirlen = len(localpath)
                if self.__check_upgrade_syncdata(SyncY.syncydb, localpath, remotepath) != 0:
                    printlog('%s ERROR: Upgrade sync info data(%s) failed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), SyncY.syncydb))
                    continue
                if SyncY.config['datacache'] == 'on':
                    self.__init_syncdata()
                else:
                    SyncY.sydblen = os.stat(SyncY.syncydb).st_size
                    SyncY.sydb = open(SyncY.syncydb, 'rb')
                if 'extraslice' in SyncY.syncpath[str(i)] and 'encryption' in SyncY.syncpath[str(i)]:
                    printlog('%s ERROR Sync path: "%s" failed, extraslice and encryption can not used together.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                    continue
                if 'encryption' in SyncY.syncpath[str(i)] and SyncY.syncpath[str(i)]['encryption'] != '0' and SyncY.syncpath[str(i)]['encryption'] != '':
                    if SyncY.syncpath[str(i)]['encryption'].upper() in ['1', '2', '3', 'ARC4', 'BLOWFISH', 'AES']:
                        if ARC4 is None or Blowfish is None or AES is None:
                            printlog('%s ERROR Sync path: "%s" failed, this path is set to encryption, but "Crypto.Cipher" library is not import.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                            continue
                        if 'encryptkey' not in SyncY.syncpath[str(i)]:
                            printlog('%s ERROR Sync path: "%s" failed, encryptkey is not set.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                            continue
                        SyncY.encryption = SyncY.syncpath[str(i)]['encryption'].upper()
                        if SyncY.encryption == 'ARC4':
                            SyncY.encryption = '1'
                        elif SyncY.encryption == 'BLOWFISH':
                            SyncY.encryption = '2'
                        elif SyncY.encryption == 'AES':
                            SyncY.encryption = '3'
                        SyncY.encryptkey = SyncY.syncpath[str(i)]['encryptkey']
                        if len(SyncY.encryptkey) < 8 or len(SyncY.encryptkey) > 56:
                            printlog('%s ERROR Sync path: "%s" failed, encryptkey is invalid, the length of encryptkey must great-than-or-equal 8 and less-than-or-equal-to 56.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                            continue
                    else:
                        printlog('%s ERROR Sync path: "%s" failed, encryption is invalid, must is ARC4 or BLOWFISH or AES.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                        continue
                else:
                    SyncY.encryption = '0'
                    SyncY.encryptkey = ''
                if 'extraslice' in SyncY.syncpath[str(i)] and SyncY.syncpath[str(i)]['extraslice'] == '1':
                    SyncY.extraslice = True
                else:
                    SyncY.extraslice = False
                if SyncY.syncpath[str(i)]['synctype'].lower() in ['0', 'u', 'upload']:
                    self.__syncy_upload(localpath, remotepath)
                elif SyncY.syncpath[str(i)]['synctype'].lower() in ['1', 'u+', 'upload+']:
                    self.__syncy_uploadplus(localpath, remotepath)
                elif SyncY.syncpath[str(i)]['synctype'].lower() in ['2', 'd', 'download']:
                    self.__syncy_download(localpath, remotepath)
                elif SyncY.syncpath[str(i)]['synctype'].lower() in ['3', 'd+', 'download+']:
                    self.__syncy_downloadplus(localpath, remotepath)
                elif SyncY.syncpath[str(i)]['synctype'].lower() in ['4', 's', 'sync']:
                    self.__syncy_sync(localpath, remotepath)
                else:
                    printlog('%s ERROR: The "synctype" of "%s" is invalid, must set to [0 - 4], skiped.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
                    continue
                if SyncY.config['datacache'] == 'on':
                    SyncY.syncData = None
                else:
                    SyncY.sydb.close()
                while True:
                    if threading.activeCount() > 1 or len(SyncY.synctask) > 0:
                        time.sleep(3)
                    else:
                        if SyncY.syncpath[str(i)]['synctype'].lower() in ['2', 'd', 'download']:
                            SyncY.syncytoken['synctotal'] += SyncY.synccount
                            self.__save_config()
                        if SyncY.failcount == 0 and SyncY.errorcount == 0:
                            if SyncY.syncpath[str(i)]['synctype'].lower() not in ['2', 'd', 'download']:
                                self.__start_compress(SyncY.syncpath[str(i)]['localpath'])
                            printlog('%s INFO: Sync path "%s" complete, Success sync %d files.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath, SyncY.synccount))
                        else:
                            printlog('%s ERROR: Sync path "%s" failed, %d files success, %d files failed, %d errors occurred.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath, SyncY.synccount, SyncY.failcount, SyncY.errorcount))
                        break
            else:
                printlog('%s ERROR: Sync path "%s" failed, local directory is not exist or is normal file.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ipath))
        self.__get_pcs_quota()

    @staticmethod
    def __test_chinese(tdir=''):
        unicode_str = '\u4e2d\u6587\u8f6c\u7801\u6d4b\u8bd5'
        unicode_str = eval('u"%s"' % unicode_str)
        unicode_str = unicode_str.encode(__CHARSET__)
        with open('%s/%s' % (tdir, unicode_str), 'w') as chnfn:
            chnfn.write(unicode_str)

    @staticmethod
    def __data_convert():
        mpath = SyncY.config['syncpath'].split(';')
        for i in range(len(mpath)):
            if mpath[i] == '':
                continue
            localdir = mpath[i].split(':')[0:1]
            syncydb = '%s/.syncy.info.db' % localdir
            if os.path.exists(syncydb):
                syncydbtmp = '%s/.syncy.info.db1' % localdir
                if os.path.exists(syncydbtmp):
                    os.remove(syncydbtmp)
                with open(syncydb, 'r') as sydb:
                    syncinfo = sydb.readlines()
                if len(syncinfo[0]) > 100 or len(syncinfo[0].split(' ')[0]) != 32:
                    printlog('%s ERROR: Convert sync data failed "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), mpath[i]))
                    continue
                with open(syncydbtmp, 'wb') as sydbnew:
                    for j in xrange(len(syncinfo)):
                        rmd5, lmtime, lsize, lmd5 = syncinfo[j].split(' ')
                        rmd5 = rmd5.decode('hex')
                        lmtime = struct.pack('>I', lmtime)
                        lsize = struct.pack('>I', lsize % 4294967296)
                        lmd5 = lmd5.decode('hex')
                        sydbnew.write('%s%s%s%s' % (rmd5, lmtime, lsize, lmd5))
                rename(syncydbtmp, syncydb)

    def __rebuild(self, mpath):
        if len(mpath) == 0:
            mpath = range(len(SyncY.syncpath))
        for i in mpath:
            i = int(i)
            if i >= len(SyncY.syncpath):
                continue
            printlog("%s INFO: Start rebuild sync data for directory '%s'." % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), SyncY.syncpath[str(i)]['localpath']))
            localpath = self.__catpath(SyncY.syncpath[str(i)]['localpath'])
            remotepath = self.__catpath(SyncY.pcsroot, SyncY.syncpath[str(i)]['remotepath'])
            SyncY.basedirlen = len(SyncY.syncpath[str(i)]['localpath'])
            SyncY.syncydb = '%s/.syncy.info.db' % SyncY.syncpath[str(i)]['localpath']
            if os.path.exists(SyncY.syncydb):
                rename(SyncY.syncydb, '%s.bak%s' % (SyncY.syncydb, str(int(time.time()))))
            if 'extraslice' in SyncY.syncpath[str(i)] and SyncY.syncpath[str(i)]['extraslice'] == '1':
                SyncY.extraslice = True
            else:
                SyncY.extraslice = False
            self.__check_upgrade_syncdata(SyncY.syncydb)
            with open(SyncY.syncydb, 'ab') as sydb:
                ret = self.__rebuild_data(localpath, remotepath, sydb)
                sydb.flush()
                os.fsync(sydb.fileno())
            if ret == 0:
                printlog("%s INFO: Rebuild sync data completed for directory '%s'." % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), SyncY.syncpath[str(i)]['localpath']))
            else:
                printlog("%s ERROR: Rebuild sync data failed for directory '%s'." % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), SyncY.syncpath[str(i)]['localpath']))

    def __rebuild_data(self, localpath, remotepath, sydb):
        startidx = 0
        retcode, rfnlist = self.__get_pcs_filelist(remotepath, startidx, SyncY.config['listnumber'])
        if retcode != 0:
            return 1
        while retcode == 0:
            for i in xrange(len(rfnlist)):
                rfullpath = rfnlist[i]['path'].encode(__CHARSET__)
                fnname = os.path.basename(rfullpath)
                lfullpath = '%s/%s' % (localpath, fnname)
                if self.__check_excludefiles(rfullpath) == 1 or self.__check_excludefiles(lfullpath) == 1:
                    continue
                if rfnlist[i]['isdir'] == 1:
                    ret = self.__rebuild_data(lfullpath, rfullpath, sydb)
                    if ret != 0:
                        return 1
                elif os.path.exists(lfullpath) and os.path.isfile(lfullpath):
                    fnstat = os.stat(lfullpath)
                    fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                    if SyncY.extraslice or rfnlist[i]['size'] == fnstat.st_size:
                        sydb.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', int(fnstat.st_mtime), fnstat.st_size, rfnlist[i]['mtime'], rfnlist[i]['size']), rfnlist[i]['md5'].decode('hex')))
            if len(rfnlist) < SyncY.config['listnumber']:
                break
            startidx += SyncY.config['listnumber']
            retcode, rfnlist = self.__get_pcs_filelist(remotepath, startidx, startidx + SyncY.config['listnumber'])
            if retcode != 0:
                return 1
        return 0

    def __check_upgrade_syncdata(self, dbfile, ldir='', rdir=''):
        dbkey = '.syncy.info.db'.ljust(16)
        dbver = '2.0'.ljust(8)
        dbauthor = 'Author:wishinlife'.ljust(20)
        dbweb = 'http://www.syncy.cn'.ljust(20)
        if not(os.path.exists(dbfile)):
            with open(dbfile, 'wb', 0) as sydb:
                sydb.write('%s%s%s%s' % (dbkey, dbver, dbauthor, dbweb))
                sydb.flush()
                os.fsync(sydb.fileno())
            return 0
        elif ldir != '' and rdir != '':
            with open(dbfile, 'rb') as sydb:
                flock(sydb, LOCK_EX)
                datarec = sydb.read(40)
                if datarec[0:24] == '%s%s' % (dbkey, dbver):
                    flock(sydb, LOCK_UN)
                    return 0
                syncdata = {}
                while datarec:
                    syncdata[datarec[24:]] = datarec[0:24]
                    datarec = sydb.read(40)
                newdbfile = '%s.v2' % dbfile
                printlog('%s INFO: Start upgrade sync info data(%s).' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), dbfile))
                with open(newdbfile, 'wb') as sydbnew:
                    flock(sydbnew, LOCK_EX)
                    sydbnew.write('%s%s%s%s' % (dbkey, dbver, dbauthor, dbweb))
                    ret = self.__upgrade_syncdata(ldir, rdir, syncdata, sydbnew)
                    sydbnew.flush()
                    os.fsync(sydbnew.fileno())
                    flock(sydbnew, LOCK_UN)
                flock(sydb, LOCK_UN)
            if ret == 0:
                rename(dbfile, dbfile + '.bak')
                rename(newdbfile, dbfile)
                printlog('%s INFO: Upgrade sync info data success, backup sync info data to "%s.bak".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), dbfile))
                return 0
            else:
                os.remove(newdbfile)
                printlog('%s ERROR: Upgrade sync info data(%s) failed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), dbfile))
                return 1
        else:
            return 1

    def __upgrade_syncdata(self, ldir, rdir, syncdata, sydbnew):
        startidx = 0
        retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, SyncY.config['listnumber'])
        if retcode != 0 and retcode != 31066:
            return 1
        lfnlist = []
        if os.path.exists(ldir):
            lfnlist = os.listdir(ldir)
            lfnlist.sort()
        while retcode == 0:
            for i in xrange(len(rfnlist)):
                rfullpath = rfnlist[i]['path'].encode(__CHARSET__)
                fnname = os.path.basename(rfullpath)
                if self.__check_excludefiles(rfullpath) == 1:
                    continue
                lfullpath = '%s/%s' % (ldir, fnname)
                if os.path.exists(lfullpath):
                    for idx in xrange(len(lfnlist)):
                        if lfnlist[idx] == fnname:
                            del lfnlist[idx]
                            break
                fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                oldfnmd5 = hashlib.md5('%s\n' % lfullpath[SyncY.basedirlen:]).digest()
                if rfnlist[i]['isdir'] == 1:
                    if os.path.exists(lfullpath) and os.path.isfile(lfullpath):
                        fmeta = os.stat(lfullpath)
                        fmtime = int(fmeta.st_mtime)
                        fsize = fmeta.st_size
                        if oldfnmd5 not in syncdata:
                            if self.__upgrade_syncdata(lfullpath, rfullpath, syncdata, sydbnew) != 0:
                                return 1
                        elif struct.pack('>II', fmtime, fsize % 4294967296) == syncdata[oldfnmd5][16:24]:
                            sydbnew.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', fmtime, fsize, fmtime, fsize), syncdata[oldfnmd5][0:16]))
                    else:
                        if self.__upgrade_syncdata(lfullpath, rfullpath, syncdata, sydbnew) != 0:
                            return 1
                else:
                    if not os.path.exists(lfullpath):
                        if oldfnmd5 in syncdata and rfnlist[i]['md5'].decode('hex') == syncdata[oldfnmd5][0:16]:
                            sydbnew.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', struct.unpack('>I', syncdata[oldfnmd5][16:20])[0], struct.unpack('>I', syncdata[oldfnmd5][20:24])[0], rfnlist[i]['mtime'], rfnlist[i]['size']), syncdata[0:16]))
                    elif os.path.exists(lfullpath) and os.path.isdir(lfullpath):
                        if oldfnmd5 not in syncdata:
                            if self.__upgrade_syncdata(lfullpath, rfullpath, syncdata, sydbnew) != 0:
                                return 1
                        elif rfnlist[i]['md5'].decode('hex') == syncdata[oldfnmd5][0:16]:
                            sydbnew.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', struct.unpack('>I', syncdata[oldfnmd5][16:20])[0], struct.unpack('>I', syncdata[oldfnmd5][20:24])[0], rfnlist[i]['mtime'], rfnlist[i]['size']), syncdata[0:16]))
                    elif oldfnmd5 in syncdata:
                        fmeta = os.stat(lfullpath)
                        fmtime = int(fmeta.st_mtime)
                        fsize = fmeta.st_size
                        if struct.pack('>II', fmtime, fsize % 4294967296) == syncdata[oldfnmd5][16:24] and rfnlist[i]['md5'].decode('hex') == syncdata[oldfnmd5][0:16]:
                            sydbnew.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', fmtime, fsize, rfnlist[i]['mtime'], rfnlist[i]['size']), syncdata[oldfnmd5][0:16]))
                        elif rfnlist[i]['md5'].decode('hex') == syncdata[oldfnmd5][0:16]:
                            sydbnew.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', struct.unpack('>I', syncdata[oldfnmd5][16:20])[0], struct.unpack('>I', syncdata[oldfnmd5][20:24])[0], rfnlist[i]['mtime'], rfnlist[i]['size']), syncdata[0:16]))
                        elif struct.pack('>II', fmtime, fsize % 4294967296) == syncdata[oldfnmd5][16:24]:
                            sydbnew.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', fmtime, fsize, struct.unpack('>I', syncdata[oldfnmd5][16:20])[0], struct.unpack('>I', syncdata[oldfnmd5][20:24])[0]), syncdata[0:16]))
                if oldfnmd5 in syncdata:
                    del syncdata[oldfnmd5]
            if len(rfnlist) < SyncY.config['listnumber']:
                break
            startidx += SyncY.config['listnumber']
            retcode, rfnlist = self.__get_pcs_filelist(rdir, startidx, startidx + SyncY.config['listnumber'])
            if retcode != 0:
                return 1
        for idx in xrange(len(lfnlist)):
            lfullpath = '%s/%s' % (ldir, lfnlist[idx])
            if lfnlist[idx][0:1] == '.' or self.__check_excludefiles(lfullpath) == 1:
                continue
            rfullpath = '%s/%s' % (rdir, lfnlist[idx])
            if os.path.isdir(lfullpath):
                if self.__upgrade_syncdata(lfullpath, rfullpath, syncdata, sydbnew) != 0:
                    return 1
            elif os.path.isfile(lfullpath):
                fmeta = os.stat(lfullpath)
                fmtime = int(fmeta.st_mtime)
                fsize = fmeta.st_size
                fnmd5 = hashlib.md5(lfullpath[SyncY.basedirlen:]).digest()
                oldfnmd5 = hashlib.md5('%s\n' % lfullpath[SyncY.basedirlen:]).digest()
                if oldfnmd5 not in syncdata:
                    continue
                elif struct.pack('>II', fmtime, fsize % 4294967296) == syncdata[oldfnmd5][16:24]:
                    sydbnew.write('%s%s%s' % (fnmd5, struct.pack('>qqqq', fmtime, fsize, fmtime, fsize), syncdata[oldfnmd5][0:16]))
                del syncdata[oldfnmd5]
        return 0

    def start(self):

        if len(self.__argv) == 0:
            if SyncY.config['syncperiod'] == '':
                self.__start_sync()
            else:
                starthour, endhour = SyncY.config['syncperiod'].split('-', 1)
                curhour = time.localtime().tm_hour
                starthour = int(starthour)
                endhour = int(endhour)
                while True:
                    if (endhour > starthour and starthour <= curhour < endhour) or (endhour < starthour and (curhour < starthour or curhour >= endhour)):
                        self.__start_sync()
                        self.__check_expires()
                        time.sleep(SyncY.config['syncinterval'])
                    else:
                        time.sleep(300)
                    curhour = time.localtime().tm_hour
        elif self.__argv[0] == 'compress':
            self.__start_compress()
        elif self.__argv[0] == 'convert':
            self.__data_convert()
        elif self.__argv[0] == 'testchinese':
            self.__test_chinese(self.__argv[1])
        elif self.__argv[0] == 'rebuild':
            self.__rebuild(self.__argv[1:])
        elif os.path.isfile(self.__argv[0]):
            fname = os.path.basename(self.__argv[0])
            if len(self.__argv) == 2:
                pcsdir = self.__catpath(SyncY.pcsroot, self.__argv[1])
            else:
                pcsdir = SyncY.pcsroot
            if self.__check_pcspath(pcsdir, fname) == 0:
                self.__upload_file_nosync(self.__argv[0], self.__catpath(pcsdir, fname))
        elif not (self.__argv[0] in ["sybind", "cpbind"]):
            printlog('%s WARNING: Unknown command "%s"' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), ' '.join(self.__argv)))


class SYCurl:
    Normal = 0
    Upload = 1
    Download = 2

    def __init__(self):
        self.__response = ''
        self.__op = None
        self.__fd = None
        self.__startpos = 0
        self.__endpos = None
        self.__buffer = ''

    @staticmethod
    def __init_cipher(crypt, key):
        if crypt == '1':
            return ARC4.new(key)
        elif crypt == '2':
            return Blowfish.new(key, Blowfish.MODE_CFB, segment_size=8)
        elif crypt == '3':
            return AES.new(key.ljust(32, '.')[0:32], AES.MODE_CFB, segment_size=8)
        else:
            return None

    def __write_data(self, rsp):
        if self.__op == SYCurl.Download:
            if self.__startpos + len(self.__buffer) + len(rsp) - 1 > self.__endpos:
                return 0
            if SyncY.encryption == '0':
                self.__fd.write(rsp)
                self.__startpos += len(rsp)
            else:
                self.__buffer += rsp
                while len(self.__buffer) >= 4096 or self.__startpos + len(self.__buffer) - 1 == self.__endpos:
                    cipher = self.__init_cipher(SyncY.encryption, SyncY.encryptkey)
                    self.__fd.write(cipher.decrypt(self.__buffer[0:4096]))
                    self.__startpos += 4096
                    self.__buffer = self.__buffer[4096:]
        else:
            self.__response += rsp
        return len(rsp)

    def __read_data(self, size):
        if self.__startpos > self.__endpos:
            return ''
        elif self.__startpos + size - 1 > self.__endpos:
            size = self.__endpos - self.__startpos + 1
        if SyncY.encryption == '0':
            self.__startpos += size
            return self.__fd.read(size)
        else:
            while len(self.__buffer) < size:
                rst = self.__fd.read(4096)
                if rst:
                    cipher = self.__init_cipher(SyncY.encryption, SyncY.encryptkey)
                    self.__buffer += cipher.encrypt(rst)
                else:
                    break
            rst = self.__buffer[0:size]
            self.__buffer = self.__buffer[size:]
            self.__startpos += size
            return rst

    @staticmethod
    def __write_header(rsp):
        return len(rsp)

    def request(self, url, querydata, rdata, method='POST', rtype=0, fnname=''):
        retrycnt = -1
        self.__op = rtype
        while retrycnt < SyncY.config['retrytimes']:
            retrycnt += 1
            if __DEBUG__:
                printlog('%s Info(%s): Start curl request(%s) %d times for %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, rdata, retrycnt, fnname))
            if self.__op != SYCurl.Normal:
                startpos, self.__endpos = rdata.split('-', 1)
                startpos = self.__startpos = int(startpos)
                self.__endpos = int(self.__endpos)
            self.__response = ''
            curl = pycurl.Curl()
            try:
                if querydata:
                    if 'path' in querydata:
                        querydata['path'] = querydata['path'].encode('UTF-8')
                    url += '?%s' % urlencode(querydata)
                curl.setopt(pycurl.URL, url)
                curl.setopt(pycurl.SSL_VERIFYPEER, 0)
                curl.setopt(pycurl.SSL_VERIFYHOST, 0)
                curl.setopt(pycurl.FOLLOWLOCATION, 1)
                curl.setopt(pycurl.CONNECTTIMEOUT, 15)
                curl.setopt(pycurl.LOW_SPEED_LIMIT, 1)
                curl.setopt(pycurl.LOW_SPEED_TIME, 30)
                curl.setopt(pycurl.USERAGENT, '')
                curl.setopt(pycurl.HEADER, 0)
                curl.setopt(pycurl.NOSIGNAL, 1)
                curl.setopt(pycurl.WRITEFUNCTION, self.__write_data)

                starthour, endhour = SyncY.config['speedlimitperiod'].split('-', 1)
                starthour = int(starthour)
                endhour = int(endhour)
                curhour = time.localtime().tm_hour
                if (endhour > starthour and starthour <= curhour < endhour) or (endhour < starthour and (curhour < starthour or curhour >= endhour)):
                    curl.setopt(pycurl.MAX_SEND_SPEED_LARGE, SyncY.config['maxsendspeed'] / SyncY.config['tasknumber'] / SyncY.config['threadnumber'])
                    curl.setopt(pycurl.MAX_RECV_SPEED_LARGE, SyncY.config['maxrecvspeed'] / SyncY.config['tasknumber'] / SyncY.config['threadnumber'])
                if self.__op == SYCurl.Upload:
                    curl.setopt(pycurl.UPLOAD, 1)
                    curl.setopt(pycurl.READFUNCTION, self.__read_data)
                    curl.setopt(pycurl.INFILESIZE, self.__endpos - startpos + 1)
                    with open(fnname, 'rb') as self.__fd:
                        self.__fd.seek(startpos)
                        flock(self.__fd, LOCK_SH)
                        curl.perform()
                        flock(self.__fd, LOCK_UN)
                elif self.__op == SYCurl.Download:
                    curl.setopt(pycurl.RANGE, rdata)
                    with open(fnname, 'rb+') as self.__fd:
                        self.__fd.seek(startpos)
                        lockf(self.__fd, LOCK_EX, self.__endpos - startpos + 1, startpos, 0)
                        curl.perform()
                        self.__fd.flush()
                        os.fsync(self.__fd.fileno())
                        lockf(self.__fd, LOCK_UN, self.__endpos - startpos + 1, startpos, 0)
                else:
                    curl.setopt(pycurl.CUSTOMREQUEST, method)
                    if method == 'POST':
                        curl.setopt(pycurl.POSTFIELDS, urlencode(rdata))
                    curl.perform()
                retcode = curl.getinfo(pycurl.HTTP_CODE)
                if retcode < 400 or retcode == 404 or retrycnt == SyncY.config['retrytimes']:
                    if retcode != 200 and retcode != 206 and self.__response == '':
                        self.__response = '{"error_code":%d,"error_msg":"Returned by the server is not in the expected results."}' % retcode
                    return retcode, self.__response
                else:
                    time.sleep(SyncY.config['retrydelay'])
            except pycurl.error, error:
                errno, errstr = error
                if retrycnt == SyncY.config['retrytimes']:
                    return errno, '{"error_code":%d,"error_msg":"%s"}' % (errno, errstr)
            except Exception, e:
                return -1, '{"error_code":%d,"error_msg":"%s"}' % (-1, traceback.format_exc().replace('\n', '\\n').replace('"', '\''))
            finally:
                curl.close()
                if __DEBUG__:
                    printlog('%s Info(%s): Complete curl request(%s) %d times for %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), threading.currentThread().name, rdata, retrycnt, fnname))


class ThreadTest(threading.Thread):
    def __init__(self):
        threading.Thread.__init__(self, name='ThreadTest')

    def run(self):
        print('%s INFO: Multi thread test success.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())))


class SYTask(threading.Thread):
    Upload = 1
    Download = 2

    def __init__(self, syncoperation, filepath, fmtime, fsize, fnmd5, pcspath, rmtime, rsize, rmd5, ondup):
        threading.Thread.__init__(self)
        self.__op = syncoperation
        self.__filepath = filepath
        self.__fmtime = fmtime
        self.__fsize = fsize
        self.__fnmd5 = fnmd5
        self.__pcspath = pcspath
        self.__rmtime = rmtime
        self.__rsize = rsize
        self.__rmd5 = rmd5
        self.__ondup = ondup
        SyncY.synctask[self.__fnmd5] = []
        self.__blocksize = SyncY.config['blocksize'] * 1048576
        if self.__op == self.Upload:
            blocksize = int(self.__fsize / 1022) + 1
            if blocksize > self.__blocksize:
                self.__blocksize = (0x100000 - (blocksize & 0xfffff) & 0xfffff) + blocksize

    def run(self):
        if __DEBUG__:
            printlog('%s Info(%s): start run task(op:%s) for %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, str(self.__op), self.__filepath))
        try:
            ret = 1
            if self.__op == SYCurl.Upload:
                if os.path.exists(self.__filepath + '.db.syy') or (SyncY.extraslice and self.__fsize > 0):
                    ret = self.__slice_uploadfile()
                else:
                    if self.__fsize <= 262144:
                        ret = self.__upload_file()
                    elif SyncY.encryption != '0':
                        if self.__fsize <= self.__blocksize + 1048576:
                            ret = self.__upload_file()
                        else:
                            ret = self.__slice_uploadfile()
                    else:
                        ret = self.__rapid_uploadfile()
            elif self.__op == SYCurl.Download:
                ret = self.__download_file()
            else:
                printlog('%s ERROR: Unknown sync operation(%s) of threading operation.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__op))
                SyncY.errorcount_increase()
            if ret == 0:
                SyncY.synccount_increase()
            else:
                SyncY.failcount_increase()
        except Exception, e:
            printlog('%s ERROR: Transfer task exception error occurred: %s .\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), e, traceback.format_exc()))
            SyncY.failcount_increase()
        finally:
            del SyncY.synctask[self.__fnmd5]
            SyncY.TaskSemaphore.release()
        if __DEBUG__:
            printlog('%s Info(%s): exit task(op:%s) for %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, str(self.__op), self.__filepath))

    def __create_emptyfile(self):
        with open('%s.syy' % self.__filepath, 'wb') as f:
            try:
                if self.__rsize > 0:
                    f.seek(self.__rsize - 1)
                    f.write('\0')
                f.flush()
                os.fsync(f.fileno())
            except Exception, e:
                printlog('%s ERROR: Create file "%s" failed. Exception: "%s".\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath, e, traceback.format_exc()))
                return 1
        return 0

    @staticmethod
    def __create_random_tmpfile(tmpfile, orig_filesize):
        try:
            randomsize = random.randint(16, 160)
            with open(tmpfile, 'wb') as f:
                for i in xrange(randomsize):
                    f.write(''.join(random.sample('zyxwvutsrqponmlkjihgfedcbaABCDEFGHIJKLMNOPQRSTUVWXYZ ,./;\'[]\\=-0987654321`~!@$%^&*()_+|}{":?><', 64)))
                extrastr = '#syy#0#%d#%d' % (orig_filesize, randomsize * 64)
                f.write(extrastr)
                f.flush()
                os.fsync(f.fileno())
            return randomsize * 64 + len(extrastr)
        except Exception, e:
            printlog('%s ERROR: Create extra tmpfile "%s" failed. Exception: "%s".\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), tmpfile, e, traceback.format_exc()))
            return 0

    def __save_data(self):
        with open(SyncY.syncydb, 'ab', 0) as sydb:
            try:
                flock(sydb, LOCK_EX)
                rmd5 = self.__rmd5.decode('hex')
                sydb.write('%s%s%s' % (self.__fnmd5, struct.pack('>qqqq', self.__fmtime, self.__fsize, self.__rmtime, self.__rsize), rmd5))
                sydb.flush()
                os.fsync(sydb.fileno())
                flock(sydb, LOCK_UN)
            except Exception, e:
                printlog('%s ERROR: Save sync data failed (%s).\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), e, traceback.format_exc()))

    def __md5sum(self):
        with open(self.__filepath, 'rb') as fh:
            m = hashlib.md5()
            fbuffer = fh.read(8192)
            while fbuffer:
                m.update(fbuffer)
                fbuffer = fh.read(8192)
            cmd5 = m.hexdigest()
        return cmd5

    def __rapid_checkcode(self):
        with open(self.__filepath, 'rb') as fh:
            m = hashlib.md5()
            fbuffer = fh.read(8192)
            crc = 0
            while fbuffer:
                m.update(fbuffer)
                crc = binascii.crc32(fbuffer, crc) & 0xffffffff
                fbuffer = fh.read(8192)
            cmd5 = m.hexdigest()
            m = hashlib.md5()
            fh.seek(0)
            for i in range(32):
                fbuffer = fh.read(8192)
                m.update(fbuffer)
        return '%x' % crc, cmd5, m.hexdigest()

    def __upload_file(self):
        if __DEBUG__:
            printlog('%s Info(%s): start upload whole file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath))
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://c.pcs.baidu.com/rest/2.0/pcs/file', {'method': 'upload', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath, 'ondup': self.__ondup}, '0-%d' % (os.stat(self.__filepath).st_size - 1), 'POST', SYCurl.Upload, self.__filepath)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            printlog('%s ERROR(Errno:%d): Upload file "%s" to PCS failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__filepath, responses['error_msg']))
            return 1
        if responses['size'] == self.__fsize:
            self.__rmd5 = responses['md5']
            self.__rmtime = responses['mtime']
            self.__rsize = responses['size']
        else:
            printlog('%s ERROR: Upload file "%s" failed, remote file size not equal to local.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
            sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'delete', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath}, '', 'POST', SYCurl.Normal)
            return 1
        self.__save_data()
        printlog('%s INFO: Upload file "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
        return 0

    def __rapid_uploadfile(self):
        if __DEBUG__:
            printlog('%s Info(%s): start rapid upload file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath))
        crc, contentmd5, slicemd5 = self.__rapid_checkcode()
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'rapidupload', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath, 'content-length': self.__fsize, 'content-md5': contentmd5, 'slice-md5': slicemd5, 'content-crc32': crc, 'ondup': self.__ondup}, '', 'POST', SYCurl.Normal)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            if responses['error_code'] == 31079:
                printlog('%s INFO: File md5 not found, upload the whole file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
                if self.__fsize <= self.__blocksize + 1048576:
                    return self.__upload_file()
                else:
                    return self.__slice_uploadfile()
            else:
                printlog('%s ERROR(Errno:%d): Rapid upload file "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__filepath, responses['error_msg']))
                return 1
        else:
            self.__rmd5 = responses['md5']
            time.sleep(1)
            retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'meta', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath}, '', 'GET', SYCurl.Normal)
            responses = json.loads(responses)
            if retcode != 200 or 'error_code' in responses:
                printlog('%s ERROR(Errno:%d): File "%s" is rapid uploaded, but get remote file\'s mate failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__filepath, responses['error_msg']))
                return 1
            responses = responses['list'][0]
            if responses['size'] == self.__fsize:
                self.__rmtime = responses['mtime']
                self.__rsize = responses['size']
            else:
                printlog('%s ERROR: File "%s" is rapid uploaded, but remote file size not equal to local.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
                return 1
            self.__save_data()
            printlog('%s INFO: Rapid upload file "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
            return 0

    def __slice_uploadfile(self):
        if __DEBUG__:
            printlog('%s Info(%s): start slice upload file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath))
        if (self.__fsize > 20 * 1073741824 and not SyncY.extraslice) or (SyncY.extraslice and self.__fsize > 20 * 1073741824 - 10340):
            printlog('%s ERROR: File "%s" size exceeds limit, max size must equal or less than 20G.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
            return 1
        if not os.path.exists('%s.db.syy' % self.__filepath):
            with open('%s.db.syy' % self.__filepath, 'w') as ulfn:
                ulfn.write('upload:%d:%d:%s\n' % (self.__fmtime, self.__fsize, SyncY.encryption))
            SyncY.synctask[self.__fnmd5].append(['upload', self.__fmtime, self.__fsize, SyncY.encryption])
        else:
            with open('%s.db.syy' % self.__filepath, 'r') as ulfn:
                ulinfo = ulfn.readline()
            if ulinfo.strip('\n') != 'upload:%d:%d:%s' % (self.__fmtime, self.__fsize, SyncY.encryption):
                with open('%s.db.syy' % self.__filepath, 'w') as ulfn:
                    ulfn.write('upload:%d:%d:%s\n' % (self.__fmtime, self.__fsize, SyncY.encryption))
                printlog('%s INFO: Local file "%s" is modified or encryption algorithm is modified, reupload the whole file.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
                SyncY.synctask[self.__fnmd5].append(['upload', self.__fmtime, self.__fsize, SyncY.encryption])
            else:
                with open('%s.db.syy' % self.__filepath, 'r') as ulfn:
                    SyncY.synctask[self.__fnmd5].append(ulfn.readline().strip('\n').split(':'))
                    SyncY.synctask[self.__fnmd5][0][2] = int(SyncY.synctask[self.__fnmd5][0][2])
                    ulinfo = ulfn.readline()
                    while ulinfo:
                        sliceinfo = ulinfo.strip('\n').split(':')[1:]
                        if sliceinfo and sliceinfo[2] == '0':
                            sliceinfo[2] = 2
                        SyncY.synctask[self.__fnmd5].append([int(sliceinfo[0]), int(sliceinfo[1]), int(sliceinfo[2]), sliceinfo[3]])
                        ulinfo = ulfn.readline()
                printlog('%s INFO: Resuming slice upload file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
        threadcond = threading.Condition()
        if threadcond.acquire():
            maxthnum = int(self.__fsize / self.__blocksize)
            if SyncY.extraslice and maxthnum == 0:
                maxthnum = 1
            elif maxthnum > SyncY.config['threadnumber']:
                maxthnum = SyncY.config['threadnumber']
            SyncY.synctask[self.__fnmd5][0].append(maxthnum)
            SyncY.synctask[self.__fnmd5][0].append([])
            for i in range(maxthnum):
                sythread = SYThread(threadcond, self.__fnmd5, self.__filepath, self.__pcspath, self.__blocksize)
                sythread.start()
            if SyncY.synctask[self.__fnmd5][0][4] > 0:
                threadcond.wait()
            threadcond.release()
        if __DEBUG__:
            printlog('%s Info(%s): all threads is exit for upload file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath))
        if len(SyncY.synctask[self.__fnmd5][0][5]) > 0:
            printlog('%s ERROR: Slice upload file "%s" failed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
            return 1
        param = {'block_list': []}
        for i in xrange(1, len(SyncY.synctask[self.__fnmd5]), 1):
            param['block_list'].append(SyncY.synctask[self.__fnmd5][i][3])
        tmpsize = 0
        if SyncY.extraslice:
            tmpfile = '%s/syncy-%s-extraslice.tmp' % (__TMP_DIR__, self.name)
            tmpsize = self.__create_random_tmpfile(tmpfile, self.__fsize)
            if tmpsize > 0:
                sycurl = SYCurl()
                retcode, responses = sycurl.request('https://c.pcs.baidu.com/rest/2.0/pcs/file', {'method': 'upload', 'access_token': SyncY.syncytoken['access_token'], 'type': 'tmpfile'}, '%d-%d' % (0, tmpsize - 1), 'POST', SYCurl.Upload, tmpfile)
                os.remove(tmpfile)
                responses = json.loads(responses)
                if retcode == 200:
                    param['block_list'].append(responses['md5'])
                else:
                    printlog('%s ERROR(Errno:%d): Upload file "%s"\'s extra slice failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__filepath, responses['error_msg']))
                    return 1
            else:
                printlog('%s ERROR: Upload file "%s"\'s extra slice failed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
                return 1
        sycurl = SYCurl()
        retcode, responses = sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'createsuperfile', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath, 'ondup': self.__ondup}, {'param': json.dumps(param)}, 'POST', SYCurl.Normal)
        responses = json.loads(responses)
        if retcode != 200 or 'error_code' in responses:
            printlog('%s ERROR(Errno:%d): Create superfile "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__filepath, responses['error_msg']))
            return 1
        os.remove('%s.db.syy' % self.__filepath)
        if responses['size'] == self.__fsize + tmpsize:
            self.__rmd5 = responses['md5']
            self.__rmtime = responses['mtime']
            self.__rsize = responses['size']
        else:
            printlog('%s ERROR: Slice upload file "%s" failed, remote file size not equal to local.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
            sycurl.request('https://pcs.baidu.com/rest/2.0/pcs/file', {'method': 'delete', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath}, '', 'POST', SYCurl.Normal)
            return 1
        self.__save_data()
        printlog('%s INFO: Slice upload file "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
        return 0

    def __download_file(self):
        if __DEBUG__:
            printlog('%s Info(%s): start download file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath))
        if os.path.exists('%s.db.syy' % self.__filepath) and os.path.exists('%s.syy' % self.__filepath):
            with open('%s.db.syy' % self.__filepath, 'r') as dlfn:
                dlinfo = dlfn.readline()
            if dlinfo.strip('\n') != 'download:%s:%d:%s' % (self.__rmd5, self.__rsize, SyncY.encryption):
                with open('%s.db.syy' % self.__filepath, 'w') as dlfn:
                    dlfn.write('download:%s:%d:%s\n' % (self.__rmd5, self.__rsize, SyncY.encryption))
                printlog('%s INFO: Remote file:"%s" is modified or encryption algorithm is modified, redownload the whole file.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__pcspath))
                os.remove(self.__filepath)
            else:
                if os.path.exists('%s.syy' % self.__filepath):
                    printlog('%s INFO: Resuming download file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__pcspath))
                else:
                    with open('%s.db.syy' % self.__filepath, 'w') as dlfn:
                        dlfn.write('download:%s:%d:%s\n' % (self.__rmd5, self.__rsize, SyncY.encryption))
        else:
            with open('%s.db.syy' % self.__filepath, 'w') as dlfn:
                dlfn.write('download:%s:%d:%s\n' % (self.__rmd5, self.__rsize, SyncY.encryption))
        if not os.path.exists('%s.syy' % self.__filepath) and self.__create_emptyfile() == 1:
            return 1
        if self.__rsize > 0:
            if self.__rsize <= self.__blocksize + 1048576:
                if __DEBUG__:
                    printlog('%s Info(%s): start download whole file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath))
                sycurl = SYCurl()
                retcode, responses = sycurl.request('https://d.pcs.baidu.com/rest/2.0/pcs/file', {'method': 'download', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath}, '0-%d' % (self.__rsize - 1), 'GET', SYCurl.Download, '%s.syy' % self.__filepath)
                if (retcode != 200 and retcode != 206) or responses != '':
                    if __DEBUG__:
                        printlog('%s Info(%s): download file "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath, responses))
                    responses = json.loads(responses)
                    printlog('%s ERROR(Errno:%d): Download file "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__pcspath, responses['error_msg']))
                    return 1
            else:
                with open('%s.db.syy' % self.__filepath, 'r') as dlfn:
                    SyncY.synctask[self.__fnmd5].append(dlfn.readline().strip('\n').split(':'))
                    SyncY.synctask[self.__fnmd5][0][2] = int(SyncY.synctask[self.__fnmd5][0][2])
                    dlinfo = dlfn.readline()
                    while dlinfo:
                        sliceinfo = dlinfo.strip('\n').split(':')[1:]
                        if sliceinfo and sliceinfo[2] == '0':
                            sliceinfo[2] = 2
                        SyncY.synctask[self.__fnmd5].append([int(sliceinfo[0]), int(sliceinfo[1]), int(sliceinfo[2]), sliceinfo[3]])
                        dlinfo = dlfn.readline()
                threadcond = threading.Condition()
                if threadcond.acquire():
                    maxthnum = int(self.__rsize / self.__blocksize)
                    if maxthnum > SyncY.config['threadnumber']:
                        maxthnum = SyncY.config['threadnumber']
                    SyncY.synctask[self.__fnmd5][0].append(maxthnum)
                    SyncY.synctask[self.__fnmd5][0].append([])
                    for i in range(maxthnum):
                        sythread = SYThread(threadcond, self.__fnmd5, self.__filepath, self.__pcspath, self.__blocksize)
                        sythread.start()
                    if SyncY.synctask[self.__fnmd5][0][4] > 0:
                        threadcond.wait()
                    threadcond.release()
                if __DEBUG__:
                    printlog('%s Info(%s): all threads is exit for download file "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, self.__filepath))
                if len(SyncY.synctask[self.__fnmd5][0][5]) > 0:
                    printlog('%s ERROR: Download file "%s" failed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__pcspath))
                    return 1
                if int(SyncY.synctask[self.__fnmd5][len(SyncY.synctask[self.__fnmd5]) - 1][1]) != self.__rsize - 1:
                    printlog('%s ERROR: Download file "%s" failed, not download all slice.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__pcspath))
                    return 1
        os.remove('%s.db.syy' % self.__filepath)
        if os.name == 'posix':
            pmeta = os.stat(os.path.dirname('%s.syy' % self.__filepath))
            os.lchown('%s.syy' % self.__filepath, pmeta.st_uid, pmeta.st_gid)
            os.chmod('%s.syy' % self.__filepath, pmeta.st_mode - stat.S_IXUSR - stat.S_IXGRP - stat.S_IXOTH)
        fmeta = os.stat('%s.syy' % self.__filepath)
        if fmeta.st_size != self.__rsize:
            printlog('%s ERROR: Download file "%s" failed, downloaded file size not equal to remote file size.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__pcspath))
            os.remove('%s.syy' % self.__filepath)
            return 1
        if SyncY.extraslice:
            with open('%s.syy' % self.__filepath, 'rb+') as f:
                if self.__rsize > 50:
                    f.seek(self.__rsize - 36)
                extraslice = f.read()
                if extraslice:
                    extraslice = extraslice.split('#')
                    if len(extraslice) == 5 and extraslice[1] == 'syy':
                        if self.__rsize == int(extraslice[3]) + int(extraslice[4]) + len(extraslice[3]) + len(extraslice[4]) + 8:
                            f.seek(int(extraslice[3]))
                            f.truncate(int(extraslice[3]))
                        else:
                            printlog('%s ERROR: Remove file "%s"\'s extra slice failed, extra slice valid failed, please manual check and remove extra slice.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath))
        if self.__rmtime != 0:
            os.utime('%s.syy' % self.__filepath, (self.__rmtime, self.__rmtime))
        self.__fmtime = self.__rmtime
        self.__fsize = fmeta.st_size
        rename('%s.syy' % self.__filepath, self.__filepath)
        self.__save_data()
        printlog('%s INFO: Download file "%s" completed.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__pcspath))
        return 0


class SYThread(threading.Thread):
    def __init__(self, threadcond, fnmd5, filepath, pcspath, blocksize):
        threading.Thread.__init__(self)
        self.__threadcond = threadcond
        self.__fnmd5 = fnmd5
        self.__filepath = filepath
        self.__pcspath = pcspath
        self.__blocksize = blocksize

    def run(self):
        if __DEBUG__:
            printlog('%s Info(%s): start thread for %s: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, SyncY.synctask[self.__fnmd5][0][0], self.__filepath))
        idx = 0
        try:
            if self.__threadcond.acquire():
                idx, startpos, endpos = self.__get_next_slice()
                self.__save_status()
                self.__threadcond.release()
            retcode = 0
            responses = None
            sycurl = SYCurl()
            while True:
                if idx == 0:
                    return 0
                elif idx == -1:
                    return 1
                if SyncY.synctask[self.__fnmd5][0][0] == 'upload':
                    if __DEBUG__:
                        printlog('%s Info(%s): Start upload slice(idx:%d) for "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, idx, self.__filepath))
                    retcode, responses = sycurl.request('https://c.pcs.baidu.com/rest/2.0/pcs/file', {'method': 'upload', 'access_token': SyncY.syncytoken['access_token'], 'type': 'tmpfile'}, '%d-%d' % (startpos, endpos), 'POST', SYCurl.Upload, self.__filepath)
                    responses = json.loads(responses)
                    if retcode != 200 or 'error_code' in responses:
                        printlog('%s ERROR(Errno:%d): Slice upload file "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__filepath, responses['error_msg']))
                        status = 2
                    else:
                        status = 1
                    if __DEBUG__:
                        printlog('%s Info(%s): Complete upload slice(idx:%d) for "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, idx, self.__filepath))
                elif SyncY.synctask[self.__fnmd5][0][0] == 'download':
                    if __DEBUG__:
                        printlog('%s Info(%s): Start download slice(idx:%d) for "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, idx, self.__filepath))
                    retcode, responses = sycurl.request('https://d.pcs.baidu.com/rest/2.0/pcs/file', {'method': 'download', 'access_token': SyncY.syncytoken['access_token'], 'path': self.__pcspath}, '%d-%d' % (startpos, endpos), 'GET', SYCurl.Download, '%s.syy' % self.__filepath)
                    if (retcode != 200 and retcode != 206) or responses != '':
                        if __DEBUG__:
                            printlog('%s Info(%s): Slice download(idx:%d) for "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, idx, self.__filepath, responses))
                        responses = json.loads(responses)
                        printlog('%s ERROR(Errno:%d): Slice download file "%s" failed: %s.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, self.__pcspath, responses['error_msg']))
                        status = 2
                    else:
                        status = 1
                    if __DEBUG__:
                        printlog('%s Info(%s): Complete download slice(idx:%d) for "%s".' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.name, idx, self.__filepath))
                else:
                    printlog('%s ERROR: Unknown operation(%s) of threading operation.' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), SyncY.synctask[self.__fnmd5][0][0]))
                    return 1
                if self.__threadcond.acquire():
                    SyncY.synctask[self.__fnmd5][idx][2] = status
                    if status == 1 and SyncY.synctask[self.__fnmd5][0][0] == 'upload':
                        SyncY.synctask[self.__fnmd5][idx][3] = responses['md5']
                    elif status == 2:
                        SyncY.synctask[self.__fnmd5][0][5].append(idx)
                    idx, startpos, endpos = self.__get_next_slice()
                    self.__save_status()
                    self.__threadcond.release()
                if len(SyncY.synctask[self.__fnmd5][0][5]) > SyncY.config['threadnumber'] * 4:
                    return 1
                retcode = 0
                responses = None
        except Exception, e:
            printlog('%s ERROR: Transfer thread exception error occurred. return code: %d, response body: %s.\n%s .\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), retcode, str(responses), e, traceback.format_exc()))
        finally:
            if self.__threadcond.acquire():
                if idx > 0:
                    SyncY.synctask[self.__fnmd5][idx][2] = 2
                    SyncY.synctask[self.__fnmd5][0][5].append(idx)
                    self.__save_status()
                SyncY.synctask[self.__fnmd5][0][4] -= 1
                if SyncY.synctask[self.__fnmd5][0][4] == 0:
                    self.__threadcond.notify()
                self.__threadcond.release()

    def __save_status(self):
        try:
            with open('%s.dbtmp.syy' % self.__filepath, 'w') as dbnew:
                dbnew.write('%s:%s:%d:%s\n' % (SyncY.synctask[self.__fnmd5][0][0], SyncY.synctask[self.__fnmd5][0][1], SyncY.synctask[self.__fnmd5][0][2], SyncY.synctask[self.__fnmd5][0][3]))
                for i in xrange(1, len(SyncY.synctask[self.__fnmd5]), 1):
                    dbnew.write('%d:%d:%d:%d:%s\n' % (i, SyncY.synctask[self.__fnmd5][i][0], SyncY.synctask[self.__fnmd5][i][1], SyncY.synctask[self.__fnmd5][i][2], SyncY.synctask[self.__fnmd5][i][3]))
                dbnew.flush()
                os.fsync(dbnew.fileno())
            rename('%s.dbtmp.syy' % self.__filepath, '%s.db.syy' % self.__filepath)
        except Exception, e:
            printlog('%s ERROR: Exception error occurred on save task status(%s). \n%s .\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath, e, traceback.format_exc()))

    def __save_status2(self, idx, startpos=0, endpos=0, status=0, rmd5='0' * 32):
        with open('%s.db.syy' % self.__filepath, 'wb') as dbfile:
            if idx < 1:
                return
            dbfile.seek(idx * 35)
            dbfile.write(struct.pack('>H2qB16s', idx, startpos, endpos, status, rmd5.decode('hex')))
            dbfile.flush()
            os.fsync(dbfile.fileno())
            dbfile.close()

    def __get_next_slice(self):
        try:
            idx, startpos, endpos = (0, 0, 0)
            for i in xrange(1, len(SyncY.synctask[self.__fnmd5]), 1):
                if SyncY.synctask[self.__fnmd5][i][2] not in [1, 0] and i not in SyncY.synctask[self.__fnmd5][0][5]:
                    idx = i
                    startpos = SyncY.synctask[self.__fnmd5][i][0]
                    endpos = SyncY.synctask[self.__fnmd5][i][1]
                    SyncY.synctask[self.__fnmd5][i][2] = 0
                    break
            if idx == 0:
                idx = len(SyncY.synctask[self.__fnmd5])
                if idx == 1:
                    startpos = 0
                else:
                    startpos = SyncY.synctask[self.__fnmd5][idx - 1][1] + 1
                filesize = SyncY.synctask[self.__fnmd5][0][2]
                if startpos == filesize:
                    return 0, 0, 0
                elif filesize - startpos > self.__blocksize + 1048576:
                    endpos = startpos + self.__blocksize - 1
                else:
                    endpos = filesize - 1
                SyncY.synctask[self.__fnmd5].append([startpos, endpos, 0, '0'])
            return idx, startpos, endpos
        except Exception, e:
            printlog('%s ERROR: Exception error occurred on get next slice of task(%s). \n%s .\n%s' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), self.__filepath, e, traceback.format_exc()))
            return -1, 0, 0

if __name__ == '__main__':
    if len(sys.argv) > 1 and sys.argv[1] == 'version':
        print(__VERSION__)
    else:
        sy = SyncY(sys.argv[1:])
        sy.start()
    sys.exit(0)
