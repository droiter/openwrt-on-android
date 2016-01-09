--[[
LuCI - Lua Configuration Interface

Copyright 2008 Steven Barth <steven@midlink.org>
Copyright 2008 Jo-Philipp Wich <xm@leipzig.freifunk.net>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

$Id: syncy.lua 2015-04-26 wishinlife $
SyncY Author: wishinlife
QQ: 57956720
E-Mail: wishinlife@gmail.com, wishinlife@qq.com
Web Home: http://www.syncy.cn
]]--

local _version = luci.sys.exec("/usr/bin/syncy.py version")
local running=(luci.sys.call("kill -0 `cat /var/run/syncy.pid`") == 0)
local cfgfile = nixio.fs.readfile("/etc/config/syncy")
local logfile = cfgfile:match("option[ ]+syncylog[ ]+'([^']*)'")
local logdesp = ""
if logfile and nixio.fs.access(logfile) then
	logdesp = "只显示最后100行日志，更多日志请查看日志文件：%s。" % logfile
else
	logdesp = "<font color=\"Red\">您还没有设置日志文件，或没有权限访问日志文件。</font>"
end

m = Map("syncy", translate("SyncY--百度网盘同步设置"), translate("<font color=\"Red\"><strong>修改配置文件前最好先停止程序，防止新修改的配置文件被程序中缓存的配置覆盖。<br/>配置文件被修改后也需要重新启动程序方可生效。</strong></font><br/>"))

s = m:section(TypedSection, "syncy", translate("SyncY"))
s.anonymous = true

s:tab("setting", translate("同步设置"))
s:tab("sylog", translate("日志"), translate(logdesp))
s:tab("about", translate("关于"))

--[[关于]]--
s:taboption("about",DummyValue,"moreinfo", translate("</label><div style=\"height:110px;padding-top:10px;\"><div style=\"float:left;width:700px;border-right:1px solid #CCC;height:110px;\" id=\"cur-ver\" curver=\"%s\">作者：WishInLife<br/>版本：%s（<span id=\"new-ver\"></span>）<br/>使用前请阅读<a target=\"_blank\" href=\"http://www.syncy.cn/index.php/about/license/\">使用协议</a><br/><strong>更多详情请访问：<a target=\"_blank\" href=\"http://www.syncy.cn\">http://www.syncy.cn</a></strong><br/><br/><span style=\"color:blue;\">如果您觉得SyncY还不错，可通过<a style=\"color: #ff0000;\" href=\"https://shenghuo.alipay.com/send/payment/fill.htm\" target=\"_blank\">支付宝</a>给作者捐赠。收款人：<span style=\"color:red;\">wishinlife@gmail.com</span><br/>感谢您对SyncY的认可和支持。</span></div><div id=\"new-donor\" style=\"float:left;width:200px;\"></div></div><label>" %{_version, _version}))
s:taboption("about",DummyValue,"moreinfo1", translate("</label><div style=\"font-weight:bold;color:red;\">注意：请勿删除本地同步根目录下的.syncy.info.db文件。<script type=\"text/javascript\" src=\"/luci-static/resources/jQ-syncy.js\"></script></div><label>"))
s:taboption("about",DummyValue,"moreinfo2", translate("</label><div style=\"font-weight:bold;\">详细帮助信息请访问：<a href=\"http://www.syncy.cn/index.php/syncyconfighelp/\" target=\"_blank\">http://www.syncy.cn/index.php/syncyconfighelp/</a></div><label>"))

--[[日志]]--
cl = s:taboption("sylog", Button, "clear", translate("清空日志"))
cl.inputstyle = "remove"
function cl.write(self, section)
	if logfile and nixio.fs.access(logfile) then
		nixio.fs.writefile(logfile, "")
	end
end

tvlog = s:taboption("sylog", TextValue, "sylogtext")
tvlog.rows = 30
tvlog.readonly = "readonly"
tvlog.wrap = "off"
function tvlog.cfgvalue(self, section)
	sylogtext = ""
	if logfile and nixio.fs.access(logfile) then
		sylogtext = luci.sys.exec("tail -n 100 %s" % logfile)
		--[[ return nixio.fs.readfile(logfile) ]]--
	end
	return sylogtext
end
--[[
function tvlog.write(self, section, value)
	value = value:gsub("\r\n?", "\n")
	nixio.fs.writefile(logfile, value)
end
]]--

--[[同步设置]]--
en=s:taboption("setting", Flag, "enabled", translate("开机自动启动"))
en.rmempty = false
en.enabled = "1"
en.disabled = "0"
function en.cfgvalue(self,section)
	return luci.sys.init.enabled("syncy") and self.enabled or self.disabled
end
function en.write(self,section,value)
	if value == "1" then
		luci.sys.call("/etc/init.d/syncy enable >/dev/null")
	else
		luci.sys.call("/etc/init.d/syncy disable >/dev/null")
	end
end

if running then
	op = s:taboption("setting", Button, "stop", translate("停止运行.."),translate("<strong><font color=\"red\">SyncY正在运行.......</font></strong>"))
	op.inputstyle = "remove"
else
	op = s:taboption("setting",Button, "start", translate("启动.."),translate("<strong>SyncY尚未启动.......</strong>"))
	op.inputstyle = "apply"
end
op.write = function(self, section)
	opstatus = (luci.sys.call("/etc/init.d/syncy %s >/dev/null" %{ self.option }) == 0)
	if self.option == "start" and opstatus then
		self.inputstyle = "remove"
		self.title = "停止运行.."
		self.description = "<strong><font color=\"red\">SyncY正在运行.......</font></strong>"
		self.option = "stop"
	elseif opstatus then
		self.inputstyle = "apply"
		self.title = "启动.."
		self.description = "<strong>SyncY尚未启动.......</strong>"
		self.option = "start"
	end
end

--[[<script language=\"JavaScript\">function myrefresh(){window.location.reload();}setTimeout('myrefresh()',5000);</script>]]--
if nixio.fs.access("/tmp/syncy.bind") then
	local usercode = nixio.fs.readfile("/tmp/syncy.bind")
	usercode = usercode:match(".*\"user_code\":\"([0-9a-z]+)\".*")
	if usercode then
		sybind = s:taboption("setting",Button, "cpbind", translate("已完成百度授权，继续帐号绑定"))
		sybind.inputstyle = "save"
		sybind.description = "<strong>绑定操作步骤：</strong><br/>1、打开百度授权页面：<a target=\"_blank\" href=\"https://openapi.baidu.com/device\">https://openapi.baidu.com/device</a><br/>2、登录百度帐号并输入用户码：<strong><font color=\"red\">%s</font></strong>，点击继续按钮完成授权<br/>3、完成授权后点击上面的按钮完成绑定操作<br/><strong><font color=\"red\">请在30分钟内完成以上操作<br/>要取消绑定操作，直接点击上面完成按钮即可（不会修改原有授权信息）</font></strong>" %{usercode}
	else
		sybind = s:taboption("setting", Button, "sybind", translate("帐号绑定/重新绑定"))
		sybind.inputstyle = "apply"
		local binded = nixio.fs.readfile("/etc/config/syncy")
		binded = binded:match(".*option (device_code) '([0-9a-z]+)'.*")
		if binded == "device_code" then
			sybind.title = "重新绑定百度帐号"
			sybind.description = "要想重新绑定必须先在百度帐号管理中解除SyncY的绑定。"
		else
			sybind.title = "绑定百度帐号"
		end
	end
else
	sybind = s:taboption("setting", Button, "sybind", translate("帐号绑定/重新绑定"))
	sybind.inputstyle = "apply"
	local binded = nixio.fs.readfile("/etc/config/syncy")
	binded = binded:match(".*option (device_code) '([0-9a-z]+)'.*")
	if binded == "device_code" then
		sybind.title = "重新绑定百度帐号"
		sybind.description = "要想重新绑定必须先在百度帐号管理中解除SyncY的绑定。"
		
	else
		sybind.title = "绑定百度帐号"
	end
end
sybind.write = function(self, section, value)
	local opstatus = luci.sys.call("/usr/bin/syncy.py %s" %{self.option})
	if self.option == "cpbind" then
		self.option = "sybind"
		self.inputstyle = "apply"
		local binded = nixio.fs.readfile("/etc/config/syncy")
		binded = binded:match(".*option (device_code) '([0-9a-z]+)'.*")
		if binded == "device_code" then
			sybind.title = "重新绑定百度帐号"
		else
			sybind.title = "绑定百度帐号"
		end
		if opstatus == 0 then
			self.description = "<strong><font color=\"red\">绑定完成！</font></strong><br/>要想重新绑定必须先在百度帐号管理中解除SyncY的绑定。"
			if running then
				luci.sys.call("/etc/init.d/syncy restart >/dev/null")
			end
		else
			self.description = "<strong><font color=\"red\">绑定失败！</font></strong>"
		end
	else
		if opstatus == 0 and nixio.fs.access("/tmp/syncy.bind") then
			local usercode = nixio.fs.readfile("/tmp/syncy.bind")
			usercode = usercode:match(".*\"user_code\":\"([0-9a-z]+)\".*")
			self.option = "sybind"
			self.inputstyle = "save"
			self.title = "已完成百度授权，继续帐号绑定"
			self.description = "<script language=\"JavaScript\">window.location=location;</script>"
		else
			self.description = "<strong><font color=\"red\">获取用户码失败！</font></strong>"
		end
	end
end


s:taboption("setting", Value, "syncylog", translate("日志文件"),translate("日志文件名必须包含完整路径名。"))
s:taboption("setting", Value, "tasknumber", translate("同时同步的任务数")).rmempty = false
s:taboption("setting", Value, "threadnumber", translate("每个任务的线程数")).rmempty = false
s:taboption("setting", Value, "blocksize", translate("分片上传下载块大小(M)")).rmempty = false
o = s:taboption("setting", ListValue, "ondup", translate("重名处理方式"))
o.default = "rename"
o:value("rename", translate("重命名文件"))
o:value("overwrite", translate("覆盖重名文件"))
o = s:taboption("setting", ListValue, "datacache", translate("是否开启缓存"))
o.default = "on"
o:value("on", translate("开启"))
o:value("off", translate("关闭"))
--[[
o = s:taboption("setting", ListValue, "slicedownload", translate("分片下载大文件"), translate("开启后将根据分片上传下载块大小的设置来分片下载大文件。"))
o.default = "on"
o:value("on", translate("开启"))
o:value("off", translate("关闭"))
o = s:taboption("setting", ListValue, "fileconsistency", translate("文件一致性检查"), translate("是否通过文件的md5值来检查上传或下载的文件与原文件是否一致，如关闭将只检查文件大小是否一致。"))
o.default = "on"
o:value("on", translate("开启"))
o:value("off", translate("关闭"))
]]--
s:taboption("setting", Value, "excludefiles", translate("排除文件")).rmempty = false
s:taboption("setting", Value, "listnumber", translate("每次检查获取远程的文件数")).rmempty = false
s:taboption("setting", Value, "retrytimes", translate("失败重试次数")).rmempty = false
s:taboption("setting", Value, "retrydelay", translate("重试延时时间(秒)")).rmempty = false
s:taboption("setting", Value, "speedlimitperiod", translate("限速时间段")).rmempty = false
s:taboption("setting", Value, "maxsendspeed", translate("最大上传速度(字节/秒)"),translate("支持单位K、M(例如：200K 或 1M)，不支持小数。")).rmempty = false
s:taboption("setting", Value, "maxrecvspeed", translate("最大下载速度(字节/秒)"),translate("支持单位K、M(例如：200K 或 1M)，不支持小数。")).rmempty = false
s:taboption("setting", Value, "syncperiod", translate("运行时间段")).rmempty = false
s:taboption("setting", Value, "syncinterval", translate("同步间隔时间(秒)")).rmempty = false

--[[同步目录设置]]--
s = m:section(TypedSection, "syncpath", translate("同步目录"))
s.anonymous = true
s.addremove = true
s.sortable  = true
s.template = "cbi/tblsection"

sen = s:option(Flag, "enable", translate("启用"))
sen.default = "1"
sen.rmempty = false
sen.enabled = "1"
sen.disabled = "0"

pth = s:option(Value, "localpath", translate("本地目录"))
pth.rmempty = false
if nixio.fs.access("/etc/config/fstab") then
        pth.titleref = luci.dispatcher.build_url("admin", "system", "fstab")
end

s:option(Value, "remotepath", translate("云端目录"), translate("必须为“/我的应用程序/SyncY”及其子目录")).rmempty = false

st = s:option(ListValue, "synctype", translate("同步类型"))
st.default = "upload"
st:value("upload", translate("0-单向上传"))
st:value("upload+", translate("1-单向上传+"))
st:value("download", translate("2-单向下载"))
st:value("download+", translate("3-单向下载+"))
st:value("sync", translate("4-双向同步"))

return m
