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

use OCA\Activity\Data;

class DataDeleteActivitiesTest extends \PHPUnit_Framework_TestCase {
	/** @var \OCA\Activity\Data */
	protected $data;

	public function setUp() {
		parent::setUp();

		$activities = array(
			array('affectedUser' => 'delete', 'subject' => 'subject', 'time' => 0),
			array('affectedUser' => 'delete', 'subject' => 'subject2', 'time' => time() - 2 * 365 * 24 * 3600),
			array('affectedUser' => 'otherUser', 'subject' => 'subject', 'time' => time()),
			array('affectedUser' => 'otherUser', 'subject' => 'subject2', 'time' => time()),
		);

		$queryActivity = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity`(`app`, `subject`, `subjectparams`, `message`, `messageparams`, `file`, `link`, `user`, `affecteduser`, `timestamp`, `priority`, `type`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
		foreach ($activities as $activity) {
			$queryActivity->execute(array(
				'app',
				$activity['subject'],
				serialize(array()),
				'',
				serialize(array()),
				'file',
				'link',
				'user',
				$activity['affectedUser'],
				$activity['time'],
				Data::PRIORITY_MEDIUM,
				'test',
			));
		}
		$this->data = new Data(
			$this->getMock('\OCP\Activity\IManager')
		);
	}

	public function tearDown() {
		$this->data->deleteActivities(array(
			'type' => 'test',
		));

		parent::tearDown();
	}

	public function deleteActivitiesData() {
		return array(
			array(array('affecteduser' => 'delete'), array('otherUser')),
			array(array('affecteduser' => array('delete', '=')), array('otherUser')),
			array(array('timestamp' => array(time() - 10, '<')), array('otherUser')),
			array(array('timestamp' => array(time() - 10, '>')), array('delete')),
		);
	}

	/**
	 * @dataProvider deleteActivitiesData
	 */
	public function testDeleteActivities($condition, $expected) {
		$this->assertUserActivities(array('delete', 'otherUser'));
		$this->data->deleteActivities($condition);
		$this->assertUserActivities($expected);
	}

	public function testExpireActivities() {
		$backgroundjob = new \OCA\Activity\BackgroundJob\ExpireActivities();
		$this->assertUserActivities(array('delete', 'otherUser'));
		$jobList = $this->getMock('\OCP\BackgroundJob\IJobList');
		$backgroundjob->execute($jobList);
		$this->assertUserActivities(array('otherUser'));
	}

	protected function assertUserActivities($expected) {
		$query = \OCP\DB::prepare("SELECT `affecteduser` FROM `*PREFIX*activity` WHERE `type` = 'test'");
		$this->assertTableKeys($expected, $query, 'affecteduser');
	}

	protected function assertTableKeys($expected, \OC_DB_StatementWrapper $query, $keyName) {
		$result = $query->execute();

		$users = array();
		while ($row = $result->fetchRow()) {
			$users[] = $row[$keyName];
		}
		$users = array_unique($users);
		sort($users);
		sort($expected);

		$this->assertEquals($expected, $users);
	}
}
