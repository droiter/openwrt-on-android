<?php

/**
 * ownCloud - Activity App
 *
 * @author Thomas Müller
 * @copyright 2014 Thomas Müller deepdiver@owncloud.com
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

use OCA\Activity\MailQueueHandler;

class MailQueueHandlerTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();
		$app = uniqid('MailQueueHandlerTest');

		$query = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity_mq` '
			. ' (`amq_appid`, `amq_subject`, `amq_subjectparams`, `amq_affecteduser`, `amq_timestamp`, `amq_type`, `amq_latest_send`) '
			. ' VALUES(?, ?, ?, ?, ?, ?, ?)');

		$query->execute(array($app, 'Test data', 'Param1', 'user1', 150, 'phpunit', 152));
		$query->execute(array($app, 'Test data', 'Param1', 'user1', 150, 'phpunit', 153));
		$query->execute(array($app, 'Test data', 'Param1', 'user2', 150, 'phpunit', 150));
		$query->execute(array($app, 'Test data', 'Param1', 'user2', 150, 'phpunit', 151));
		$query->execute(array($app, 'Test data', 'Param1', 'user3', 150, 'phpunit', 154));
		$query->execute(array($app, 'Test data', 'Param1', 'user3', 150, 'phpunit', 155));
	}

	public function tearDown() {
		$query = \OCP\DB::prepare('DELETE FROM `*PREFIX*activity_mq`');
		$query->execute();

		parent::tearDown();
	}

	public function getAffectedUsersData()
	{
		return array(
			array(null, array('user2', 'user1', 'user3')),
			array(5, array('user2', 'user1', 'user3')),
			array(3, array('user2', 'user1', 'user3')),
			array(2, array('user2', 'user1')),
			array(1, array('user2')),
		);
	}

	/**
	 * @dataProvider getAffectedUsersData
	 */
	public function testGetAffectedUsers($limit, $expected) {
		$mq = new MailQueueHandler();
		$users = $mq->getAffectedUsers($limit);

		$this->assertEquals($expected, $users);
	}
}
