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
	OC_User::setIncognitoMode(true);

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

$ownerView = new \OC\Files\View('/' . $user . '/files');

$mime = $ownerView->getMimeType($img);
list($mimePart,) = explode('/', $mime);
if ($mimePart === 'image') {
	$fileInfo = $ownerView->getFileInfo($img);
	if($fileInfo['encrypted'] === true) {
		$local = $ownerView->toTmpFile($img);
	} else {
		$local = $ownerView->getLocalFile($img);
	}
	$rotate = false;
	if (is_callable('exif_read_data')) { //don't use OCP\Image here, using OCP\Image will always cause parsing the image file
		$exif = @exif_read_data($local, 'IFD0');
		if (isset($exif['Orientation'])) {
			$rotate = ($exif['Orientation'] > 1);
		}
	}
	
	OCP\Response::setContentDispositionHeader(basename($img), 'attachment');
	
	if ($rotate) {
		$image = new OCP\Image($local);
		$image->fixOrientation();
		$image->show();
	} else { //use the original file if we dont need to rotate, saves having to re-encode the image
		header('Content-Type: ' . $mime);
		$ownerView->readfile($img);
	}
}
