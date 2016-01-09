--[[
LuCI - Lua Configuration Interface - aria2 support

Copyright 2014 nanpuyue <nanpuyue@gmail.com>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0
]]--

module("luci.controller.aria2", package.seeall)

function index()
	if not nixio.fs.access("/etc/config/aria2") then
		return
	end

	local page = entry({"admin", "services", "aria2"}, cbi("aria2"), _("Aria2 Settings"))
	page.dependent = true

end
