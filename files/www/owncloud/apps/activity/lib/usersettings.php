<?php

/**
 * ownCloud - Activity App
 *
 * @author Joas Schilling
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
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
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Activity;

use \OCP\Config;

/**
 * Class UserSettings
 *
 * @package OCA\Activity
 */
class UserSettings
{
	const EMAIL_SEND_HOURLY = 0;
	const EMAIL_SEND_DAILY = 1;
	const EMAIL_SEND_WEEKLY = 2;

	/**
	 * Get a setting for a user
	 *
	 * Falls back to some good default values if the user does not have a preference
	 *
	 * @param string $user
	 * @param string $method Should be one of 'stream', 'email' or 'setting'
	 * @param string $type One of the activity types, 'batchtime' or 'self'
	 * @return mixed
	 */
	public static function getUserSetting($user, $method, $type) {
		return Config::getUserValue(
			$user,
			'activity',
			'notify_' . $method . '_' . $type,
			self::getDefaultSetting($method, $type)
		);
	}

	/**
	 * Get a good default setting for a preference
	 *
	 * @param string $method Should be one of 'stream', 'email' or 'setting'
	 * @param string $type One of the activity types, 'batchtime' or 'self'
	 * @return bool|int
	 */
	public static function getDefaultSetting($method, $type) {
		if ($method == 'setting') {
			if ($type == 'batchtime') {
				return 3600;
			} else if ($type == 'self') {
				return true;
			}
		}

		$settings = self::getDefaultTypes($method);
		return in_array($type, $settings);
	}

	/**
	 * Get the default selection of types for a method
	 *
	 * @param string $method Should be one of 'stream' or 'email'
	 * @return array Array of strings
	 */
	public static function getDefaultTypes($method) {
		$settings = array();
		switch ($method) {
			case 'stream':
				$settings[] = Data::TYPE_SHARE_CREATED;
				$settings[] = Data::TYPE_SHARE_CHANGED;
				$settings[] = Data::TYPE_SHARE_DELETED;
//				$settings[] = Data::TYPE_SHARE_RESHARED;
				$settings[] = Data::TYPE_SHARE_RESTORED;

//				$settings[] = Data::TYPE_SHARE_DOWNLOADED;

			case 'email':
				$settings[] = Data::TYPE_SHARED;
//				$settings[] = Data::TYPE_SHARE_EXPIRED;
//				$settings[] = Data::TYPE_SHARE_UNSHARED;
//
//				$settings[] = Data::TYPE_SHARE_UPLOADED;
//
//				$settings[] = Data::TYPE_STORAGE_QUOTA_90;
//				$settings[] = Data::TYPE_STORAGE_FAILURE;
		}

		// Allow other apps to add notification types to the default setting
		$additionalSettings = \OC::$server->getActivityManager()->getDefaultTypes($method);
		$settings = array_merge($settings, $additionalSettings);

		return $settings;
	}

	/**
	 * Get a list with enabled notification types for a user
	 *
	 * @param string	$user	Name of the user
	 * @param string	$method	Should be one of 'stream' or 'email'
	 * @return array
	 */
	public static function getNotificationTypes($user, $method) {
		$l = \OC_L10N::get('activity');
		$data = new Data(\OC::$server->getActivityManager());
		$types = $data->getNotificationTypes($l);

		$notificationTypes = array();
		foreach ($types as $type => $desc) {
			if (self::getUserSetting($user, $method, $type)) {
				$notificationTypes[] = $type;
			}
		}

		return $notificationTypes;
	}

	/**
	 * Filters the given user array by their notification setting
	 *
	 * @param array $users
	 * @param string $method
	 * @param string $type
	 * @return array Returns a "username => b:true" Map for method = stream
	 *               Returns a "username => i:batchtime" Map for method = email
	 */
	public static function filterUsersBySetting($users, $method, $type) {
		if (empty($users) || !is_array($users)) {
			return array();
		}

		$preferences = new \OC\Preferences(\OC_DB::getConnection());
		$filteredUsers = array();

		$potentialUsers = $preferences->getValueForUsers('activity', 'notify_' . $method . '_' . $type, $users);
		foreach ($potentialUsers as $user => $value) {
			if ($value) {
				$filteredUsers[$user] = true;
			}
			unset($users[array_search($user, $users)]);
		}

		// Get the batch time setting from the database
		if ($method == 'email') {
			$potentialUsers = $preferences->getValueForUsers('activity', 'notify_setting_batchtime', array_keys($filteredUsers));
			foreach ($potentialUsers as $user => $value) {
				$filteredUsers[$user] = $value;
			}
		}

		if (empty($users)) {
			return $filteredUsers;
		}

		// If the setting is enabled by default,
		// we add all users that didn't set the preference yet.
		if (UserSettings::getDefaultSetting($method, $type)) {
			foreach ($users as $user) {
				if ($method == 'stream') {
					$filteredUsers[$user] = true;
				} else {
					$filteredUsers[$user] = self::getDefaultSetting('setting', 'batchtime');
				}
			}
		}

		return $filteredUsers;
	}
}
