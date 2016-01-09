<?php

/**
 * ownCloud - Updater plugin
 *
 * @author Victor Dubiniuk
 * @copyright 2013 Victor Dubiniuk victor.dubiniuk@gmail.com
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
$packageUrl = isset($decodedRequest['url']) ? $decodedRequest['url'] : '';
$packageVersion = isset($decodedRequest['version']) ? $decodedRequest['version'] : '';
$backupPath = isset($decodedRequest['backupPath']) ? $decodedRequest['backupPath'] : '';

try {
	Updater::update($packageVersion, $backupPath);
	
	// We are done. Some cleanup
	Downloader::cleanUp($packageVersion);
	Updater::cleanUp();
	\OCP\JSON::success();
} catch (\Exception $e){
	App::log($e->getMessage());
	\OCP\JSON::error(array(
		'message' => (string) App::$l10n->t('Update failed.') . $e->getMessage()
	));
}
