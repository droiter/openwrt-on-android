#
# Copyright (C) 2014 nanpuyue <nanpuyue@gmail.com>
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=dnscrypt-proxy
PKG_VERSION:=1.4.0
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.bz2
PKG_SOURCE_URL:=http://download.dnscrypt.org/dnscrypt-proxy/
PKG_MD5SUM:=40b5b73f5042330b86084460d7c839c6

PKG_FIXUP:=autoreconf
PKG_INSTALL:=1
PKG_BUILD_PARALLEL:=1
PKG_BUILD_DEPENDS:=libsodium

include $(INCLUDE_DIR)/package.mk

define Package/dnscrypt-proxy/default
  SECTION:=net
  CATEGORY:=Network
  SUBMENU:=IP Addresses and Names
  URL:=http://dnscrypt.org/
  DEPENDS:=+libsodium
  MAINTAINER:=nanpuyue <nanpuyue@gmail.com>
endef

define Package/dnscrypt-proxy
  $(call Package/dnscrypt-proxy/default)
  TITLE:=A tool for securing communications between a client and a DNS resolver
endef

define Package/dnscrypt-proxy/description
	dnscrypt-proxy provides local service which can be used directly as your local resolver or as a DNS forwarder, encrypting and authenticating requests using the DNSCrypt protocol and passing them to an upstream server.
	The DNSCrypt protocol uses high-speed high-security elliptic-curve cryptography and is very similar to DNSCurve, but focuses on securing communications between a client and its first-level resolver.
	While not providing end-to-end security, it protects the local network, which is often the weakest point of the chain, against man-in-the-middle attacks. It also provides some confidentiality to DNS queries.
endef

define Package/hostip
  $(call Package/dnscrypt-proxy/default)
  TITLE:=A tool for resolving a name to IPv4 or IPv6 addresses
endef

define Package/hostip/description
	The DNSCrypt proxy ships with a simple tool named hostip that resolves a name to IPv4 or IPv6 addresses.
	This tool can be useful for starting some services before dnscrypt-proxy.
	Queries made by hostip are not authenticated.
endef

CONFIGURE_ARGS += \
	--prefix=/usr

TARGET_CFLAGS += \
	-fdata-sections \
	-ffunction-sections

TARGET_LDFLAGS += \
	-Wl,-gc-sections

MAKE_FLAGS += \
	CFLAGS="$(TARGET_CFLAGS)" \
	LDFLAGS="$(TARGET_LDFLAGS)"

define Package/dnscrypt-proxy/conffiles
/etc/config/dnscrypt-proxy
endef

define Package/dnscrypt-proxy/install
	$(INSTALL_DIR) $(1)/usr/sbin
	$(INSTALL_BIN) $(PKG_INSTALL_DIR)/usr/sbin/dnscrypt-proxy $(1)/usr/sbin/
	$(INSTALL_DIR) $(1)/etc/config
	$(INSTALL_DATA) files/config $(1)/etc/config/dnscrypt-proxy
	$(INSTALL_DIR) $(1)/etc/init.d
	$(INSTALL_BIN) files/init $(1)/etc/init.d/dnscrypt-proxy
endef

define Package/hostip/install
	$(INSTALL_DIR) $(1)/usr/bin
	$(INSTALL_BIN) $(PKG_INSTALL_DIR)/usr/bin/hostip $(1)/usr/bin/
endef

$(eval $(call BuildPackage,dnscrypt-proxy))
$(eval $(call BuildPackage,hostip))
