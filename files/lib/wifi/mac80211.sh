#!/bin/sh
append DRIVERS "mac80211"

lookup_phy() {
	[ -n "$phy" ] && {
		[ -d /sys/class/ieee80211/$phy ] && return
	}

	local devpath
	config_get devpath "$device" path
	[ -n "$devpath" ] && {
		for _phy in /sys/devices/$devpath/ieee80211/phy*; do
			[ -e "$_phy" ] && {
				phy="${_phy##*/}"
				return
			}
		done
	}

	local macaddr="$(config_get "$device" macaddr | tr 'A-Z' 'a-z')"
	[ -n "$macaddr" ] && {
		for _phy in /sys/class/ieee80211/*; do
			[ -e "$_phy" ] || continue

			[ "$macaddr" = "$(cat ${_phy}/macaddress)" ] || continue
			phy="${_phy##*/}"
			return
		done
	}
	phy=
	return
}

find_mac80211_phy() {
	local device="$1"

	config_get phy "$device" phy
	lookup_phy
	[ -n "$phy" -a -d "/sys/class/ieee80211/$phy" ] || {
		echo "PHY for wifi device $1 not found"
		return 1
	}
	config_set "$device" phy "$phy"

	config_get macaddr "$device" macaddr
	[ -z "$macaddr" ] && {
		config_set "$device" macaddr "$(cat /sys/class/ieee80211/${phy}/macaddress)"
	}

	return 0
}

# same as wireless/mac80211.sh
get_phy_interface() {
        local phy="$1"
        if [ -d "/sys/class/ieee80211/${phy}/device/net" ]; then
                for xfile in /sys/class/ieee80211/${phy}/device/net/wlan*
                do
                     xphy1=$(/usr/bin/realpath $xfile/phy80211)
                     xphy2=$(/usr/bin/realpath /sys/class/ieee80211/${phy})
                     if [ $xphy1 = $xphy2 ]; then
                        echo $(basename $xfile) 2>/dev/null;
                        break
                     fi
                done
        fi
}


check_mac80211_device() {
	config_get phy "$1" phy
	[ -z "$phy" ] && {
		find_mac80211_phy "$1" >/dev/null || return 0
		config_get phy "$1" phy
	}
	[ "$phy" = "$dev" ] && found=1
}

detect_mac80211() {
	devidx=0
	config_load wireless
	while :; do
		config_get type "radio$devidx" type
                config_get xpath "radio$devidx" path
		[ -n "$type" ] || break
                #echo "---" $xpath
                eval xapath_$devidx=$xpath
                #eval echo "ar \$devidx \$xapath_$devidx"
		devidx=$(($devidx + 1))
	done

	for _dev in /sys/class/ieee80211/*; do
		[ -e "$_dev" ] || continue

		dev="${_dev##*/}"

		found=0
		config_foreach check_mac80211_device wifi-device
		[ "$found" -gt 0 ] && continue

		mode_band="g"
		channel="11"
		htmode=""
		ht_capab=""

		iw phy "$dev" info | grep -q 'Capabilities:' && htmode=HT20
		iw phy "$dev" info | grep -q '2412 MHz' || { mode_band="a"; channel="36"; }

		vht_cap=$(iw phy "$dev" info | grep -c 'VHT Capabilities')
		cap_5ghz=$(iw phy "$dev" info | grep -c "Band 2")
		[ "$vht_cap" -gt 0 -a "$cap_5ghz" -gt 0 ] && {
			mode_band="a";
			channel="36"
			htmode="VHT80"
		}

		[ -n $htmode ] && append ht_capab "	option htmode	$htmode" "$N"

		if [ -x /usr/bin/readlink -a -h /sys/class/ieee80211/${dev} ]; then
			path="$(readlink -f /sys/class/ieee80211/${dev}/device)"
		else
			path=""
		fi
		if [ -n "$path" ]; then
			path="${path##/sys/devices/}"
			dev_id="	option path	'$path'"
		else
			dev_id="	option macaddr	$(cat /sys/class/ieee80211/${dev}/macaddress)"
		fi

                #echo "xxxx $path"

                eval xapath_$devidx=$path
                #eval echo "xxxar $devidx \$xapath_$devidx"

                xii=0
                xfound=0
                while [ $xii -lt $devidx ]
                do
                    #eval echo "===" \$xapath_$devidx ?= $path
                    if eval [ \$xapath_$devidx = \"$path\" ]
                    then
                       #echo "==found"
                       xfound=1
                       break
                    fi
                    xii=$(($xii+1))
                done

               [ $xfound -gt 0 ] && continue

               xxifname=$(get_phy_interface "$dev")

		cat <<EOF
config wifi-device  radio$devidx
	option type     mac80211
	option channel  ${channel}
	option hwmode	11${mode_band}
$dev_id
$ht_capab
	# REMOVE THIS LINE TO ENABLE WIFI:
	option disabled 1

config wifi-iface
	option device   radio$devidx
	option network  'lan $xxifname'
	option mode     ap
	option ssid     OpenWrt
	option key 'asdf1234'
	option encryption 'psk+tkip'

EOF
	devidx=$(($devidx + 1))
	done
}

