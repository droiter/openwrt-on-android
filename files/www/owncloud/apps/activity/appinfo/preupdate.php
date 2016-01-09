<?php

$installedVersion=OCP\Config::getAppValue('activity', 'installed_version');
if (version_compare($installedVersion, '1.1.1', '>=') && version_compare($installedVersion, '1.1.2', '<=')) {
	$connection = OC_DB::getConnection();
	$platform = $connection->getDatabasePlatform();
	if ($platform->getName() === 'oracle') {
		try {
			$connection->beginTransaction();
			$sql1 = 'ALTER TABLE `*PREFIX*activity` ADD `type_text` VARCHAR2(255) DEFAULT NULL';
			\OC_DB::executeAudited($sql1, array());
			$sql2 = 'UPDATE `*PREFIX*activity` SET `type_text` = to_char(`type`)';
			\OC_DB::executeAudited($sql2, array());
			$sql3 = 'ALTER TABLE `*PREFIX*activity` DROP COLUMN `type` cascade constraints';
			\OC_DB::executeAudited($sql3, array());
			$sql4 = 'ALTER TABLE `*PREFIX*activity` RENAME COLUMN `type_text` TO `type`';
			\OC_DB::executeAudited($sql4, array());
			$connection->commit();
		} catch (\DatabaseException $e) {
			\OCP\Util::writeLog('activity', "Oracle upgrade fixup failed: " . $e->getMessage(), \OCP\Util::WARN);
		}
	}
}
