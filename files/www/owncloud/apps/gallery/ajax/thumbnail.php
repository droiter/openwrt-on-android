<?php
/**
 * Copyright (c) 2012 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

OCP\JSON::checkAppEnabled('gallery');

list($owner, $img) = explode('/', $_GET['file'], 2);
$linkItem = \OCP\Share::getShareByToken($owner);

if (is_array($linkItem) && isset($linkItem['uid_owner'])) {
	// seems to be a valid share
	$rootLinkItem = \OCP\Share::resolveReShare($linkItem);
	$user = $rootLinkItem['uid_owner'];

	// Setup filesystem
	OCP\JSON::checkUserExists($user);
	OC_Util::tearDownFS();
	OC_Util::setupFS($user);

	$fullPath = \OC\Files\Filesystem::getPath($linkItem['file_source']);
	if($fullPath === null) {
		exit();
	}
	$img = trim($fullPath . '/' . $img);
} else {
	OCP\JSON::checkLoggedIn();
	$user = OCP\User::getUser();
}

session_write_close();

$square = isset($_GET['square']) ? (bool)$_GET['square'] : false;

$image = new \OCA\Gallery\Thumbnail('/' . $img, $user, $square);
$image->show();
