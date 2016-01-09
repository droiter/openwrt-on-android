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

use \OCP\Activity\IManager;

class GroupHelper
{
	/** @var array */
	protected $activities = array();

	/** @var array */
	protected $openGroup = array();

	/** @var string */
	protected $groupKey = '';

	/** @var int */
	protected $groupTime = 0;

	/** @var bool */
	protected $allowGrouping;

	/** @var \OCP\Activity\IManager */
	protected $activityManager;

	/** @var \OCA\Activity\DataHelper */
	protected $dataHelper;

	/**
	 * @param \OCP\Activity\IManager $activityManager
	 * @param \OCA\Activity\DataHelper $dataHelper
	 * @param bool $allowGrouping
	 */
	public function __construct(IManager $activityManager, DataHelper $dataHelper, $allowGrouping) {
		$this->allowGrouping = $allowGrouping;

		$this->activityManager = $activityManager;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * Add an activity to the internal array
	 *
	 * @param array $activity
	 */
	public function addActivity($activity) {
		$activity['subjectparams_array'] = unserialize($activity['subjectparams']);
		if (!is_array($activity['subjectparams_array'])) {
			$activity['subjectparams_array'] = array($activity['subjectparams_array']);
		}

		$activity['messageparams_array'] = unserialize($activity['messageparams']);
		if (!is_array($activity['messageparams_array'])) {
			$activity['messageparams_array'] = array($activity['messageparams_array']);
		}

		if (!$this->getGroupKey($activity)) {
			if (!empty($this->openGroup)) {
				$this->activities[] = $this->openGroup;
				$this->openGroup = array();
				$this->groupKey = '';
				$this->groupTime = 0;
			}
			$this->activities[] = $activity;
			return;
		}

		// Only group when the event has the same group key
		// and the time difference is not bigger than 3 days.
		if ($this->getGroupKey($activity) === $this->groupKey &&
			abs($activity['timestamp'] - $this->groupTime) < (3 * 24 * 60 * 60)
		) {
			$parameter = $this->getGroupParameter($activity);
			if ($parameter !== false) {
				if (!is_array($this->openGroup['subjectparams_array'][$parameter])) {
					$this->openGroup['subjectparams_array'][$parameter] = array($this->openGroup['subjectparams_array'][$parameter]);
				}
				if (!isset($this->openGroup['activity_ids'])) {
					$this->openGroup['activity_ids'] = array((int) $this->openGroup['activity_id']);
				}

				$this->openGroup['subjectparams_array'][$parameter][] = $activity['subjectparams_array'][$parameter];
				$this->openGroup['subjectparams_array'][$parameter] = array_unique($this->openGroup['subjectparams_array'][$parameter]);
				$this->openGroup['activity_ids'][] = (int) $activity['activity_id'];
			}
		} else {
			if (!empty($this->openGroup)) {
				$this->activities[] = $this->openGroup;
			}

			$this->groupKey = $this->getGroupKey($activity);
			$this->groupTime = $activity['timestamp'];
			$this->openGroup = $activity;
		}
	}

	/**
	 * Get grouping key for an activity
	 *
	 * @param array $activity
	 * @return false|string False, if grouping is not allowed, grouping key otherwise
	 */
	protected function getGroupKey($activity) {
		if ($this->getGroupParameter($activity) === false) {
			return false;
		}
		return $activity['app'] . '|' . $activity['user'] . '|' . $activity['subject'];
	}

	protected function getGroupParameter($activity) {
		if (!$this->allowGrouping) {
			return false;
		}

		if ($activity['app'] === 'files') {
			switch ($activity['subject']) {
				case 'created_self':
				case 'created_by':
				case 'changed_self':
				case 'changed_by':
				case 'deleted_self':
				case 'deleted_by':
				case 'restored_self':
				case 'restored_by':
					return 0;
			}
		}

		// Allow other apps to group their notifications
		return $this->activityManager->getGroupParameter($activity);
	}

	/**
	 * Get the prepared activities
	 *
	 * @return array translated activities ready for use
	 */
	public function getActivities() {
		if (!empty($this->openGroup)) {
			$this->activities[] = $this->openGroup;
		}

		$return = array();
		foreach ($this->activities as $activity) {
			$activity = $this->dataHelper->formatStrings($activity, 'subject');
			$activity = $this->dataHelper->formatStrings($activity, 'message');

			$activity['typeicon'] = $this->dataHelper->getTypeIcon($activity['type']);
			$return[] = $activity;
		}

		return $return;
	}
}
