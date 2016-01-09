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

use OCA\Activity\DataHelper;
use OCA\Activity\GroupHelper;
use OCA\Activity\ParameterHelper;

class GroupHelperTest extends \PHPUnit_Framework_TestCase {
	public function groupHelperData() {
		return array(
			array(
				true,
				array(),
				array(),
			),
			array(
				false,
				array(),
				array(),
			),
			array(
				true,
				array(
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file1.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time(),
					),
				),
				array(
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file1.txt',
						'typeicon'		=> 'icon-add-color',
					),
				),
			),
			array(
				true,
				array(
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file1.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time(),
					),
				),
				array(
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file1.txt',
						'typeicon'		=> 'icon-add-color',
					),
				),
			),
			array(
				true,
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file2.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file2.txt',
						'timestamp'		=> time(),
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file1.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time(),
					),
				),
				array(
					array(
						'activity_id'	=> 2,
						'activity_ids'	=> array(2, 1),
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array(array(
							'testing/file2.txt',
							'testing/file1.txt',
						)),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file2.txt',
						'typeicon'		=> 'icon-add-color',
					),
				),
			),
			array(
				true,
				array(
					array(
						'activity_id'	=> 3,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file3.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file3.txt',
						'timestamp'		=> time(),
					),
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file2.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file2.txt',
						'timestamp'		=> time(),
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'shared',
						'subject'		=> 'shared_link_self',
						'subjectparams'	=> serialize(array('testing/file1.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time() - 10,
					),
				),
				array(
					array(
						'activity_id'	=> 3,
						'activity_ids'	=> array(3, 2),
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array(array(
							'testing/file3.txt',
							'testing/file2.txt',
						)),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file3.txt',
						'typeicon'		=> 'icon-add-color',
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'shared',
						'subject'		=> 'shared_link_self',
						'subjectparams'	=> array('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file1.txt',
						'typeicon'		=> 'icon-share',
					),
				),
			),
			array(
				true,
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file2.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file2.txt',
						'timestamp'		=> time(),
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> serialize(''),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time() - 1000000,
					),
				),
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file2.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file2.txt',
						'typeicon'		=> 'icon-add-color',
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> array(''),
						'file'			=> 'testing/file1.txt',
						'typeicon'		=> 'icon-add-color',
					),
				),
			),
			array(
				false,
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file2.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file2.txt',
						'timestamp'		=> time(),
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file1.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time(),
					),
				),
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file2.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file2.txt',
						'typeicon'		=> 'icon-add-color',
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file1.txt',
						'typeicon'		=> 'icon-add-color',
					),
				),
			),
			array(
				true,
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'NOTfiles',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file2.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file2.txt',
						'timestamp'		=> time(),
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'NOTfiles',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> serialize(array('testing/file1.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time(),
					),
				),
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'NOTfiles',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file2.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file2.txt',
						'typeicon'		=> 'icon-add-color',
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'NOTfiles',
						'type'			=> 'file_created',
						'subject'		=> 'created_self',
						'subjectparams'	=> array('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file1.txt',
						'typeicon'		=> 'icon-add-color',
					),
				),
			),
			array(
				true,
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'NOTcreated_self',
						'subjectparams'	=> serialize(array('testing/file2.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file2.txt',
						'timestamp'		=> time(),
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'NOTcreated_self',
						'subjectparams'	=> serialize(array('testing/file1.txt')),
						'message'		=> '',
						'messageparams'	=> serialize(array()),
						'file'			=> 'testing/file1.txt',
						'timestamp'		=> time(),
					),
				),
				array(
					array(
						'activity_id'	=> 2,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'NOTcreated_self',
						'subjectparams'	=> array('testing/file2.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file2.txt',
						'typeicon'		=> 'icon-add-color',
					),
					array(
						'activity_id'	=> 1,
						'user'			=> 'user',
						'affecteduser'	=> 'affecteduser',
						'app'			=> 'files',
						'type'			=> 'file_created',
						'subject'		=> 'NOTcreated_self',
						'subjectparams'	=> array('testing/file1.txt'),
						'message'		=> '',
						'messageparams'	=> array(),
						'file'			=> 'testing/file1.txt',
						'typeicon'		=> 'icon-add-color',
					),
				),
			),
		);
	}

	/**
	 * @dataProvider groupHelperData
	 */
	public function testGroupHelper($allowGrouping, $activities, $expected) {
		$activityManager = $this->getMock('\OCP\Activity\IManager');
		$activityManager->expects($this->any())
			->method('getGroupParameter')
			->with($this->anything())
			->will($this->returnValue(false));
		$activityLanguage = \OCP\Util::getL10N('activity');

		$helper = new GroupHelper(
			$activityManager,
			new DataHelper(
				$activityManager,
				new ParameterHelper(
					new \OC\Files\View(''),
					$this->getMockBuilder('OCP\IConfig')->disableOriginalConstructor()->getMock(),
					$activityLanguage
				),
				$activityLanguage
			),
			$allowGrouping
		);

		foreach ($activities as $activity) {
			$helper->addActivity($activity);
		}

		$result = $helper->getActivities();
		$clearedResult = array();
		foreach ($result as $activity) {
			unset($activity['subjectformatted']);
			unset($activity['messageformatted']);
			unset($activity['timestamp']);
			$clearedResult[] = $activity;
		}
		$this->assertEquals($expected, $clearedResult);
	}
}
