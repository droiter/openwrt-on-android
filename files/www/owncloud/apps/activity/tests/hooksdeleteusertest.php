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
use OCA\Activity\Hooks;

class HooksDeleteUserTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
		parent::setUp();

		$activities = array(
			array('affectedUser' => 'delete', 'subject' => 'subject'),
			array('affectedUser' => 'delete', 'subject' => 'subject2'),
			array('affectedUser' => 'otherUser', 'subject' => 'subject'),
			array('affectedUser' => 'otherUser', 'subject' => 'subject2'),
		);

		$queryActivity = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity`(`app`, `subject`, `subjectparams`, `message`, `messageparams`, `file`, `link`, `user`, `affecteduser`, `timestamp`, `priority`, `type`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
		$queryMailQueue = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity_mq`(`amq_appid`, `amq_subject`, `amq_subjectparams`, `amq_affecteduser`, `amq_timestamp`, `amq_type`, `amq_latest_send`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?)');
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
				time(),
				Data::PRIORITY_MEDIUM,
				'test',
			));
			$queryMailQueue->execute(array(
				'app',
				$activity['subject'],
				serialize(array()),
				$activity['affectedUser'],
				time(),
				'test',
				time() + 10,
			));
		}
	}

	public function tearDown() {
		parent::tearDown();

		$data = new Data(
			$this->getMock('\OCP\Activity\IManager')
		);
		$data->deleteActivities(array(
			'type' => 'test',
		));
		$query = \OCP\DB::prepare("DELETE FROM `*PREFIX*activity_mq` WHERE `amq_type` = 'test'");
		$query->execute();
	}

	public function testHooksDeleteUser() {

		$this->assertUserActivities(array('delete', 'otherUser'));
		$this->assertUserMailQueue(array('delete', 'otherUser'));
		Hooks::deleteUser(array('uid' => 'delete'));
		$this->assertUserActivities(array('otherUser'));
		$this->assertUserMailQueue(array('otherUser'));
	}

	protected function assertUserActivities($expected) {
		$query = \OCP\DB::prepare("SELECT `affecteduser` FROM `*PREFIX*activity` WHERE `type` = 'test'");
		$this->assertTableKeys($expected, $query, 'affecteduser');
	}

	protected function assertUserMailQueue($expected) {
		$query = \OCP\DB::prepare("SELECT `amq_affecteduser` FROM `*PREFIX*activity_mq` WHERE `amq_type` = 'test'");
		$this->assertTableKeys($expected, $query, 'amq_affecteduser');
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
