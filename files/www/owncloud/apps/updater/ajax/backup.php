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
clearstatcache();

try {
	
	// Url to download package e.g. http://download.owncloud.org/releases/owncloud-4.0.5.tar.bz2
	$packageUrl = '';

	//Package version e.g. 4.0.4
	$packageVersion = '';
	$updateData = \OC_Updater::check();

	if (isset($updateData['version']) && $updateData['version'] !== Array()){
		$packageVersion = $updateData['version'];
	}
	if (isset($updateData['url']) && $updateData['url'] !== Array()){
		$packageUrl = $updateData['url'];
	}
	if (!strlen($packageVersion) || !strlen($packageUrl)) {
		App::log('Invalid response from update feed.');
		throw new \Exception((string) App::$l10n->t('Version not found'));
	}
	
	//Some cleanup first
	Downloader::cleanUp($packageVersion);
	if (!Downloader::isClean($packageVersion)){
		$message = App::$l10n->t('Upgrade is not possible. Your web server does not have permission to remove the following directory:');
		$message .= '<br />' . Downloader::getPackageDir($packageVersion);
		$message .= '<br />' . App::$l10n->t('Update permissions on this directory and its content or remove it manually first.');
		throw new \Exception($message);
	}

	Updater::cleanUp();
	if (!Updater::isClean()){
		$message = App::$l10n->t('Upgrade is not possible. Your web server does not have permission to remove the following directory:');
		$message .= '<br />' . Updater::getTempDir();
		$message .= '<br />' . App::$l10n->t('Update permissions on this directory and its content or remove it manually first.');
		throw new \Exception($message);
	}
	
	$backupPath = Backup::create();
	\OCP\JSON::success(array(
		'backup' => $backupPath,
		'version' => $packageVersion,
		'url' => $packageUrl
	));
	
} catch (PermissionException $e){
	//Something is not writable|readable
	\OCP\JSON::error(array(
		'message' => $e->getExtendedMessage()
	));
} catch (FsException $e){
	//Backup failed
	App::log($e->getMessage());
	\OCP\JSON::error(array(
		'message' => $e->getMessage()
	));
} catch (\Exception $e){
	//Something went wrong. We don't know what
	App::log($e->getMessage());
	\OCP\JSON::error(array(
		'message' => $e->getMessage()
	));
}
