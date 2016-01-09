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

namespace OCA\Activity\BackgroundJob;

/**
 * Class ExpireActivities
 *
 * @package OCA\Activity\BackgroundJob
 */
class ExpireActivities extends \OC\BackgroundJob\TimedJob {
	public function __construct() {
		// Run once per day
		$this->setInterval(60 * 60 * 24);
	}

	protected function run($argument) {
		// Remove activities that are older then one year
		$expireDays = \OCP\Config::getSystemValue('activity_expire_days', 365);
		$data = new \OCA\Activity\Data(
			\OC::$server->getActivityManager()
		);
		$data->expire($expireDays);
	}
}
