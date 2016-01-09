OpenWrt-Extra
=============

Some extra packages for OpenWrt

Add "src-git extra git://github.com/nanpuyue/openwrt-extra.git" to feeds.conf.default.

```bash
./scripts/feeds update -a
./scripts/feeds install -a
```

the list of packages:
* dnscrypt-proxy
* libsodium (depended by dnscrypt-proxy, but have no ipk)
* luci-app-aira2
* shadowsocks-libev
* webui-aria2
* yaaw
