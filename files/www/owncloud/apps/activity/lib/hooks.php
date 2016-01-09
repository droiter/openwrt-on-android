<?php

/**
 * ownCloud - Activities App
 *
 * @author Frank Karlitschek, Joas Schilling
 * @copyright 2013 Frank Karlitschek frank@owncloud.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Activity;

/**
 * @brief The class to handle the filesystem hooks
 */
class Hooks {
	/**
	 * @brief Registers the filesystem hooks for basic filesystem operations.
	 * All other events has to be triggered by the apps.
	 */
	public static function register() {
		\OCP\Util::connectHook('OC_Filesystem', 'post_create', 'OCA\Activity\Hooks', 'fileCreate');
		\OCP\Util::connectHook('OC_Filesystem', 'post_update', 'OCA\Activity\Hooks', 'fileUpdate');
		\OCP\Util::connectHook('OC_Filesystem', 'delete', 'OCA\Activity\Hooks', 'fileDelete');
		\OCP\Util::connectHook('\OCA\Files_Trashbin\Trashbin', 'post_restore', 'OCA\Activity\Hooks', 'fileRestore');
		\OCP\Util::connectHook('OCP\Share', 'post_shared', 'OCA\Activity\Hooks', 'share');

		\OCP\Util::connectHook('OC_User', 'post_deleteUser', 'OCA\Activity\Hooks', 'deleteUser');

		// hooking up the activity manager
		$am = \OC::$server->getActivityManager();
		$am->registerConsumer(function() {
			return new Consumer();
		});
	}

	/**
	 * @brief Store the create hook events
	 * @param array $params The hook params
	 */
	public static function fileCreate($params) {
		if (\OCP\User::getUser() !== false) {
			self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_CREATED, 'created_self', 'created_by');
		} else {
			self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_CREATED, '', 'created_public');
		}
	}

	/**
	 * @brief Store the update hook events
	 * @param array $params The hook params
	 */
	public static function fileUpdate($params) {
		self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_CHANGED, 'changed_self', 'changed_by');
	}

	/**
	 * @brief Store the delete hook events
	 * @param array $params The hook params
	 */
	public static function fileDelete($params) {
		self::addNotificationsForFileAction($params['path'], Data::TYPE_SHARE_DELETED, 'deleted_self', 'deleted_by');
	}

	/**
	 * @brief Store the restore hook events
	 * @param array $params The hook params
	 */
	public static function fileRestore($params) {
		self::addNotificationsForFileAction($params['filePath'], Data::TYPE_SHARE_RESTORED, 'restored_self', 'restored_by');
	}

	/**
	 * Creates the entries for file actions on $file_path
	 *
	 * @param string $filePath         The file that is being changed
	 * @param int    $activityType     The activity type
	 * @param string $subject          The subject for the actor
	 * @param string $subjectBy        The subject for other users (with "by $actor")
	 */
	public static function addNotificationsForFileAction($filePath, $activityType, $subject, $subjectBy) {
		// Do not add activities for .part-files
		if (substr($filePath, -5) === '.part') {
			return;
		}

		$affectedUsers = self::getUserPathsFromPath($filePath);
		$filteredStreamUsers = UserSettings::filterUsersBySetting(array_keys($affectedUsers), 'stream', $activityType);
		$filteredEmailUsers = UserSettings::filterUsersBySetting(array_keys($affectedUsers), 'email', $activityType);

		foreach ($affectedUsers as $user => $path) {
			if (empty($filteredStreamUsers[$user]) && empty($filteredEmailUsers[$user])) {
				continue;
			}

			if ($user === \OCP\User::getUser()) {
				if (!UserSettings::getUserSetting(\OCP\User::getUser(), 'setting', 'self')) {
					continue;
				}

				$userSubject = $subject;
				$userParams = array($path);
			} else {
				$userSubject = $subjectBy;
				$userParams = array($path, \OCP\User::getUser());
			}

			self::addNotificationsForUser(
				$user, $userSubject, $userParams,
				$path, true,
				!empty($filteredStreamUsers[$user]),
				!empty($filteredEmailUsers[$user]) ? $filteredEmailUsers[$user] : 0,
				$activityType, Data::PRIORITY_HIGH
			);
		}
	}

	/**
	 * Returns a "username => path" map for all affected users
	 *
	 * @param string $path
	 * @return array
	 */
	public static function getUserPathsFromPath($path) {
		list($file_path, $uidOwner) = self::getSourcePathAndOwner($path);
		return \OCP\Share::getUsersSharingFile($file_path, $uidOwner, true, true);
	}

	/**
	 * Return the source
	 *
	 * @param string $path
	 * @return array
	 */
	public static function getSourcePathAndOwner($path) {
		$uidOwner = \OC\Files\Filesystem::getOwner($path);

		if ($uidOwner != \OCP\User::getUser()) {
			\OC\Files\Filesystem::initMountPoints($uidOwner);
			$info = \OC\Files\Filesystem::getFileInfo($path);
			$ownerView = new \OC\Files\View('/'.$uidOwner.'/files');
			$path = $ownerView->getPath($info['fileid']);
		}

		return array($path, $uidOwner);
	}

	/**
	 * @brief Manage sharing events
	 * @param array $params The hook params
	 */
	public static function share($params) {
		if ($params['itemType'] === 'file' || $params['itemType'] === 'folder') {
			if ($params['shareWith']) {
				if ($params['shareType'] == \OCP\Share::SHARE_TYPE_USER) {
					self::shareFileOrFolderWithUser($params);
				} else if ($params['shareType'] == \OCP\Share::SHARE_TYPE_GROUP) {
					self::shareFileOrFolderWithGroup($params);
				}
			} else {
				self::shareFileOrFolder($params);
			}
		}
	}

	/**
	 * @brief Sharing a file or folder with a user
	 * @param array $params The hook params
	 */
	public static function shareFileOrFolderWithUser($params) {
		// User performing the share
		self::shareNotificationForSharer('shared_user_self', $params['shareWith'], $params['fileSource'], $params['itemType']);

		// New shared user
		$path = $params['fileTarget'];
		self::addNotificationsForUser(
			$params['shareWith'], 'shared_with_by', array($path, \OCP\User::getUser()),
			$path, ($params['itemType'] === 'file'),
			UserSettings::getUserSetting($params['shareWith'], 'stream', Data::TYPE_SHARED),
			UserSettings::getUserSetting($params['shareWith'], 'email', Data::TYPE_SHARED) ? UserSettings::getUserSetting($params['shareWith'], 'setting', 'batchtime') : 0
		);
	}

	/**
	 * @brief Sharing a file or folder with a group
	 * @param array $params The hook params
	 */
	public static function shareFileOrFolderWithGroup($params) {
		// User performing the share
		self::shareNotificationForSharer('shared_group_self', $params['shareWith'], $params['fileSource'], $params['itemType']);

		// Members of the new group
		$affectedUsers = array();
		$usersInGroup = \OC_Group::usersInGroup($params['shareWith']);
		foreach ($usersInGroup as $user) {
			$affectedUsers[$user] = $params['fileTarget'];
		}

		// Remove the triggering user, we already managed his notifications
		unset($affectedUsers[\OCP\User::getUser()]);

		if (empty($affectedUsers)) {
			return;
		}

		$filteredStreamUsersInGroup = UserSettings::filterUsersBySetting($usersInGroup, 'stream', Data::TYPE_SHARED);
		$filteredEmailUsersInGroup = UserSettings::filterUsersBySetting($usersInGroup, 'email', Data::TYPE_SHARED);

		// Check when there was a naming conflict and the target is different
		// for some of the users
		$query = \OCP\DB::prepare('SELECT `share_with`, `file_target` FROM `*PREFIX*share` WHERE `parent` = ? ');
		$result = $query->execute(array($params['id']));
		if (\OCP\DB::isError($result)) {
			\OCP\Util::writeLog('OCA\Activity\Hooks::shareFileOrFolderWithGroup', \OCP\DB::getErrorMessage($result), \OCP\Util::ERROR);
		} else {
			while ($row = $result->fetchRow()) {
				$affectedUsers[$row['share_with']] = $row['file_target'];
			}
		}

		foreach ($affectedUsers as $user => $path) {
			if (empty($filteredStreamUsersInGroup[$user]) && empty($filteredEmailUsersInGroup[$user])) {
				continue;
			}

			self::addNotificationsForUser(
				$user, 'shared_with_by', array($path, \OCP\User::getUser()),
				$path, ($params['itemType'] === 'file'),
				!empty($filteredStreamUsersInGroup[$user]),
				!empty($filteredEmailUsersInGroup[$user]) ? $filteredEmailUsersInGroup[$user] : 0
			);
		}
	}

	/**
	 * Add notifications for the user that shares a file/folder
	 *
	 * @param string $subject
	 * @param string $shareWith
	 * @param int $fileSource
	 * @param string $itemType
	 */
	public static function shareNotificationForSharer($subject, $shareWith, $fileSource, $itemType) {
		// User performing the share
		if (UserSettings::getUserSetting(\OCP\User::getUser(), 'setting', 'self')) {
			$file_path = \OC\Files\Filesystem::getPath($fileSource);

			self::addNotificationsForUser(
				\OCP\User::getUser(), $subject, array($file_path, $shareWith),
				$file_path, ($itemType === 'file'),
				UserSettings::getUserSetting(\OCP\User::getUser(), 'stream', Data::TYPE_SHARED),
				UserSettings::getUserSetting(\OCP\User::getUser(), 'email', Data::TYPE_SHARED) ? UserSettings::getUserSetting(\OCP\User::getUser(), 'setting', 'batchtime') : 0
			);
		}
	}

	/**
	 * Adds the activity and email for a user when the settings require it
	 *
	 * @param string $user
	 * @param string $subject
	 * @param array $subjectParams
	 * @param string $path
	 * @param bool $isFile If the item is a file, we link to the parent directory
	 * @param bool $streamSetting
	 * @param int $emailSetting
	 * @param string $type
	 * @param int $priority
	 */
	protected static function addNotificationsForUser($user, $subject, $subjectParams, $path, $isFile, $streamSetting, $emailSetting, $type = Data::TYPE_SHARED, $priority = Data::PRIORITY_MEDIUM) {
		$link = \OCP\Util::linkToAbsolute('files', 'index.php', array(
			'dir' => ($isFile) ? dirname($path) : $path,
		));

		// Add activity to stream
		if ($streamSetting) {
			Data::send('files', $subject, $subjectParams, '', array(), $path, $link, $user, $type, $priority);
		}

		// Add activity to mail queue
		if ($emailSetting) {
			$latestSend = time() + $emailSetting;
			Data::storeMail('files', $subject, $subjectParams, $user, $type, $latestSend);
		}
	}

	/**
	 * @brief Sharing a file or folder via link/public
	 * @param array $params The hook params
	 */
	public static function shareFileOrFolder($params) {
		if (UserSettings::getUserSetting(\OCP\User::getUser(), 'setting', 'self') &&
			UserSettings::getUserSetting(\OCP\User::getUser(), 'stream', Data::TYPE_SHARED)) {

			$path = \OC\Files\Filesystem::getPath($params['fileSource']);
			$link = \OCP\Util::linkToAbsolute('files', 'index.php', array(
				'dir' => ($params['itemType'] === 'file') ? dirname($path) : $path,
			));

			Data::send('files', 'shared_link_self', array($path), '', array(), $path, $link, \OCP\User::getUser(), Data::TYPE_SHARED, Data::PRIORITY_MEDIUM);
		}
	}

	/**
	 * Delete remaining activities and emails when a user is deleted
	 * @param array $params The hook params
	 */
	public static function deleteUser($params) {
		// Delete activity entries
		$data = new Data(
			\OC::$server->getActivityManager()
		);
		$data->deleteActivities(array('affecteduser' => $params['uid']));

		// Delete entries from mail queue
		$query = \OCP\DB::prepare(
			'DELETE FROM `*PREFIX*activity_mq` '
			. ' WHERE `amq_affecteduser` = ?');
		$query->execute(array($params['uid']));
	}
}
