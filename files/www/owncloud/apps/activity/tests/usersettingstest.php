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
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Activity\Tests;

use OCA\Activity\UserSettings;
use OCA\Activity\Data;

class UserSettingsTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
		$preferences = array(
			array('test1', 'activity', 'notify_stream_type1', '1'),
			array('test1', 'activity', 'notify_stream_type2', '2'),
			array('test2', 'activity', 'notify_stream_type1', '0'),
			array('test2', 'activity', 'notify_stream_type2', '0'),
			array('test3', 'activity', 'notify_stream_type1', ''),
			array('test4', 'activity', 'notify_stream_type1', '3'),
			array('test6', 'activity', 'notify_stream_file_nodefault', '1'),
			array('test6', 'activity', 'notify_stream_file_created', '1'),

			array('test1', 'activity', 'notify_email_type1', '1'),
			array('test1', 'activity', 'notify_email_type2', '2'),
			array('test2', 'activity', 'notify_email_type1', '0'),
			array('test2', 'activity', 'notify_email_type2', '0'),
			array('test3', 'activity', 'notify_email_type1', ''),
			array('test4', 'activity', 'notify_email_type1', '3'),
			array('test5', 'activity', 'notify_email_type1', '1'),
			array('test6', 'activity', 'notify_email_shared', '1'),
			array('test6', 'activity', 'notify_email_file_created', '1'),

			array('test1', 'activity', 'notify_setting_batchtime', '1'),
			array('test2', 'activity', 'notify_setting_batchtime', '2'),
			array('test3', 'activity', 'notify_setting_batchtime', '3'),
			array('test4', 'activity', 'notify_setting_batchtime', '4'),
			array('test6', 'activity', 'notify_setting_batchtime', '2700'),
		);

		$query = \OCP\DB::prepare('INSERT INTO `*PREFIX*preferences`(`userid`, `appid`, `configkey`, `configvalue`)' . ' VALUES(?, ?, ?, ?)');
		foreach ($preferences as $preference) {
			$query->execute($preference);
		}
	}

	public function tearDown() {
		$query = \OCP\DB::prepare('DELETE FROM `*PREFIX*preferences` WHERE `appid` = ?');
		$query->execute(array('activity'));
	}

	public function getDefaultSettingData() {
		return array(
			array('stream', Data::TYPE_SHARED, true),
			array('stream', Data::TYPE_SHARE_CREATED, true),
			array('email', Data::TYPE_SHARED, true),
			array('email', Data::TYPE_SHARE_CREATED, false),
			array('setting', 'batchtime', 3600),
		);
	}

	/**
	 * @dataProvider getDefaultSettingData
	 */
	public function testGetDefaultSetting($method, $type, $expected) {
		$this->assertEquals($expected, UserSettings::getDefaultSetting($method, $type));
	}

	public function getNotificationTypesData() {
		return array(
			array('test1', 'stream', array('shared', 'file_created', 'file_changed', 'file_deleted', 'file_restored')),
			array('noPreferences', 'email', array('shared')),
		);
	}

	/**
	 * @dataProvider getNotificationTypesData
	 */
	public function testGetNotificationTypes($user, $method, $expected) {
		$this->assertEquals($expected, UserSettings::getNotificationTypes($user, $method));
	}

	public function filterUsersBySettingData() {
		return array(
			array(array(), 'stream', 'type1', array()),
			array(array('test', 'test1', 'test2', 'test3', 'test4'), 'stream', 'type1', array('test1' => true, 'test4' => true)),
			array(array('test', 'test1', 'test2', 'test3', 'test4', 'test5'), 'email', 'type1', array('test1' => '1', 'test4' => '4', 'test5' => true)),
			array(array('test', 'test6'), 'stream', 'file_created', array('test' => true, 'test6' => true)),
			array(array('test', 'test6'), 'stream', 'file_nodefault', array('test6' => true)),
			array(array('test6'), 'email', 'shared', array('test6' => '2700')),
			array(array('test', 'test6'), 'email', 'shared', array('test' => '3600', 'test6' => '2700')),
			array(array('test', 'test6'), 'email', 'file_created', array('test6' => '2700')),
		);
	}

	/**
	 * @dataProvider filterUsersBySettingData
	 */
	public function testFilterUsersBySetting($users, $method, $type, $expected) {
		$this->assertEquals($expected, UserSettings::filterUsersBySetting($users, $method, $type));
	}
}
