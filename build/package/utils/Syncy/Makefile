include $(TOPDIR)/rules.mk

PKG_NAME:=SyncY-Python-luci
PKG_VERSION:=2.5.3
PKG_RELEASE:=1

PKG_BUILD_DIR := $(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/$(PKG_NAME)
        SECTION:=utils
        CATEGORY:=Utilities
        DEPENDS:=kmod-nls-utf8 libopenssl libcurl python python-curl
        TITLE:=syncY
        PKGARCH:=all
        MAINTAINER:=WishInLife
endef

define Package/$(PKG_NAME)/description
        Baidu netdisk synchronization software.
endef

define Build/Prepare
endef

define Build/Configure
endef

define Build/Compile
endef

define Package/$(PKG_NAME)/conffiles
	/etc/config/syncy
endef

define Package/$(PKG_NAME)/install
	# $(CP) ./files/* $(1)/

	#install shell
	$(INSTALL_DIR) $(1)/etc/init.d
	$(INSTALL_BIN) ./files/init.d/syncy $(1)/etc/init.d/syncy
	#install config
	$(INSTALL_DIR) $(1)/etc/config
	$(INSTALL_CONF) ./files/config/syncy $(1)/etc/config/syncy
	#install execute bin
	$(INSTALL_DIR) $(1)/usr/bin
	$(INSTALL_BIN) ./files/syncy.py $(1)/usr/bin/syncy.py
	#install luci controller
	$(INSTALL_DIR) $(1)/usr/lib/lua/luci/controller
	$(INSTALL_DATA) ./files/controller/syncy.lua $(1)/usr/lib/lua/luci/controller/syncy.lua
	#install luci model
	$(INSTALL_DIR) $(1)/usr/lib/lua/luci/model/cbi
	$(INSTALL_DATA) ./files/model/cbi/syncy.lua $(1)/usr/lib/lua/luci/model/cbi/syncy.lua
	#install javascript
	$(INSTALL_DIR) $(1)/www/luci-static/resources
	$(INSTALL_DATA) ./files/resources/jQ-syncy.js $(1)/www/luci-static/resources/jQ-syncy.js
endef

define Package/$(PKG_NAME)/preinst
	#!/bin/sh
	[ -f /etc/config/syncy ] && cp /etc/config/syncy /etc/config/syncy.bak.syy
	exit 0
endef

define Package/$(PKG_NAME)/postinst
	#!/bin/sh
	[ -f /etc/config/syncy.bak.syy ] && mv /etc/config/syncy.bak.syy /etc/config/syncy
	chmod 644 /etc/config/syncy
	chmod 755 /etc/init.d/syncy
	chmod 755 /usr/bin/syncy.py
	chmod 644 /usr/lib/lua/luci/controller/syncy.lua
	chmod 644 /usr/lib/lua/luci/model/cbi/syncy.lua
	chmod 644 /www/luci-static/resources/jQ-syncy.js
	exit 0
endef

define Package/$(PKG_NAME)/prerm
	#!/bin/sh
	[ -f /etc/config/syncy ] && cp /etc/config/syncy /etc/config/syncy.bak.syy
	exit 0
endef

define Package/$(PKG_NAME)/postrm
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
