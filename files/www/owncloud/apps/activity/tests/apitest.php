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

class ApiTest extends \PHPUnit_Framework_TestCase {
	protected $originalWEBROOT;

	public function setUp() {
		parent::setUp();
		$this->originalWEBROOT =\OC::$WEBROOT;
		\OC::$WEBROOT = '';
		\OC_User::createUser('activity-api-user1', 'activity-api-user1');
		\OC_User::createUser('activity-api-user2', 'activity-api-user2');

		$activities = array(
			array(
				'affectedUser' => 'activity-api-user1',
				'subject' => 'created_self',
				'subjectparams' => array('/A/B.txt'),
				'type' => 'file_created',
			),
			array(
				'affectedUser' => 'activity-api-user1',
				'subject' => 'deleted_by',
				'subjectparams' => array('/A/B.txt', 'Deleter'),
				'type' => 'file_deleted',
			),
		);

		$queryActivity = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity`(`app`, `subject`, `subjectparams`, `message`, `messageparams`, `file`, `link`, `user`, `affecteduser`, `timestamp`, `priority`, `type`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
		$loop = 0;
		foreach ($activities as $activity) {
			$queryActivity->execute(array(
				'files',
				$activity['subject'],
				serialize($activity['subjectparams']),
				'',
				serialize(array()),
				'file',
				'link',
				'user',
				$activity['affectedUser'],
				time() + $loop,
				Data::PRIORITY_MEDIUM,
				$activity['type'],
			));
			$loop++;
		}
	}

	public function tearDown() {
		$data = new Data(
			$this->getMock('\OCP\Activity\IManager')
		);

		$data->deleteActivities(array(
			'affecteduser' => 'activity-api-user1',
		));
		\OC_User::deleteUser('activity-api-user1');
		$data->deleteActivities(array(
			'affecteduser' => 'activity-api-user2',
		));
		\OC_User::deleteUser('activity-api-user2');
		$data->deleteActivities(array(
			'type' => 'test',
		));
		\OC::$WEBROOT = $this->originalWEBROOT;
		parent::tearDown();
	}

	public function getData() {
		return array(
			array('activity-api-user2', 0, 30, array()),
			array('activity-api-user1', 0, 30, array(
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'Deleter deleted A/B.txt',
				),
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'You created A/B.txt',
				),
			)),
			array('activity-api-user1', 0, 1, array(
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'Deleter deleted A/B.txt',
				),
			)),
			array('activity-api-user1', 1, 1, array(
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'You created A/B.txt',
				),
			)),
		);
	}

	/**
	 * @dataProvider getData
	 */
	public function testGet($user, $start, $count, $expected) {
		$_GET['start'] = $start;
		$_GET['count'] = $count;
		\OC_User::setUserId($user);
		$this->assertEquals($user, \OC_User::getUser());
		$result = \OCA\Activity\Api::get(array('_route' => 'get_cloud_activity'));

		$this->assertEquals(100, $result->getStatusCode());
		$data = $result->getData();
		$this->assertEquals(sizeof($expected), sizeof($data));

		while (!empty($expected)) {
			$assertExpected = array_shift($expected);
			$assertData = array_shift($data);
			foreach ($assertExpected as $key => $value) {
				$this->assertArrayHasKey($key, $assertData);
				if ($value !== null) {
					$this->assertEquals($value, $assertData[$key]);
				}
			}
		}
	}
}
