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

module("luci.controller.syncy", package.seeall)

function index()
	if not nixio.fs.access("/etc/config/syncy") then
		return
	end

	local page
	page = entry({"admin", "services", "syncy"}, cbi("syncy"), _("SyncY"))
	page.dependent = true
end
