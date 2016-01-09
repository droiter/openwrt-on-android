<?php

/**
 * ownCloud - Updater plugin
 *
 * @author Victor Dubiniuk
 * @copyright 2014 Victor Dubiniuk victor.dubiniuk@gmail.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

namespace OCA\Updater;

\OCP\JSON::checkAdminUser();
\OCP\JSON::callCheck();

set_time_limit(0);

$request = file_get_contents('php://input');
$decodedRequest = json_decode($request, true);

// Downloading new version
$packageUrl = isset($decodedRequest['url']) ? $decodedRequest['url'] : '';
$packageVersion = isset($decodedRequest['version']) ? $decodedRequest['version'] : '';

try {
	Downloader::getPackage($packageUrl, $packageVersion);
	\OCP\JSON::success();
} catch (\Exception $e) {
	App::log($e->getMessage());
	\OCP\JSON::error(array(
		'message' => $e->getMessage()
	));
}
