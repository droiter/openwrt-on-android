<?php
/**
 * Copyright (c) 2013 Bart Visscher <bartv@thisnet.nl>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

$installedVersion=OCP\Config::getAppValue('calendar', 'installed_version');
if (version_compare($installedVersion, '0.6.3', '<')) {
	// TODO: This is a quick and dirty solution, this code needs to move into core
	$connection = OC_DB::getConnection();
	$sm = $connection->getSchemaManager();
	$prefix = OC_Config::getValue('dbtableprefix', 'oc_' );
	try {
		$sm->renameTable($prefix.'calendar_objects', $prefix.'clndr_objects');
		$sm->renameTable($prefix.'calendar_calendars', $prefix.'clndr_calendars');
		$sm->renameTable($prefix.'calendar_share_event', $prefix.'clndr_share_event');
		$sm->renameTable($prefix.'calendar_share_calendar', $prefix.'clndr_share_calendar');
		$sm->renameTable($prefix.'calendar_repeat', $prefix.'clndr_repeat');
	} catch (Exception $e) {
		\OCP\Util::writeLog('calendar', 'preupdate: '.$e->getMessage(), \OCP\Util::ERROR);
	}
}
