<?php

/**
 * ownCloud - Activity App
 *
 * @author Frank Karlitschek
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

use \OCP\DB;
use \OCP\User;
use \OCP\Util;

/**
 * @brief Class for managing the data in the activities
 */
class Data
{
	const PRIORITY_VERYLOW 	= 10;
	const PRIORITY_LOW	= 20;
	const PRIORITY_MEDIUM	= 30;
	const PRIORITY_HIGH	= 40;
	const PRIORITY_VERYHIGH	= 50;

	const TYPE_SHARED = 'shared';
	const TYPE_SHARE_EXPIRED = 'share_expired';
	const TYPE_SHARE_UNSHARED = 'share_unshared';

	const TYPE_SHARE_CREATED = 'file_created';
	const TYPE_SHARE_CHANGED = 'file_changed';
	const TYPE_SHARE_DELETED = 'file_deleted';
	const TYPE_SHARE_RESHARED = 'file_reshared';
	const TYPE_SHARE_RESTORED = 'file_restored';

	const TYPE_SHARE_DOWNLOADED = 'file_downloaded';
	const TYPE_SHARE_UPLOADED = 'file_uploaded';

	const TYPE_STORAGE_QUOTA_90 = 'storage_quota_90';
	const TYPE_STORAGE_FAILURE = 'storage_failure';

	/** @var \OCP\Activity\IManager */
	protected $activityManager;

	public function __construct(\OCP\Activity\IManager $activityManager){
		$this->activityManager = $activityManager;
	}

	protected $notificationTypes = array();

	/**
	 * @param \OC_L10N $l
	 * @return array Array "stringID of the type" => "translated string description for the setting"
	 */
	public function getNotificationTypes(\OC_L10N $l) {
		if (isset($this->notificationTypes[$l->getLanguageCode()]))
		{
			return $this->notificationTypes[$l->getLanguageCode()];
		}

		$notificationTypes = array(
			self::TYPE_SHARED => $l->t('A file or folder has been <strong>shared</strong>'),
//			self::TYPE_SHARE_UNSHARED => $l->t('Previously shared file or folder has been <strong>unshared</strong>'),
//			self::TYPE_SHARE_EXPIRED => $l->t('Expiration date of shared file or folder <strong>expired</strong>'),
			self::TYPE_SHARE_CREATED => $l->t('A new file or folder has been <strong>created</strong>'),
			self::TYPE_SHARE_CHANGED => $l->t('A file or folder has been <strong>changed</strong>'),
			self::TYPE_SHARE_DELETED => $l->t('A file or folder has been <strong>deleted</strong>'),
//			self::TYPE_SHARE_RESHARED => $l->t('A file or folder has been <strong>reshared</strong>'),
			self::TYPE_SHARE_RESTORED => $l->t('A file or folder has been <strong>restored</strong>'),
//			self::TYPE_SHARE_DOWNLOADED => $l->t('A file or folder shared via link has been <strong>downloaded</strong>'),
//			self::TYPE_SHARE_UPLOADED => $l->t('A file has been <strong>uploaded</strong> into a folder shared via link'),
//			self::TYPE_STORAGE_QUOTA_90 => $l->t('<strong>Storage usage</strong> is at 90%%'),
//			self::TYPE_STORAGE_FAILURE => $l->t('An <strong>external storage</strong> has an error'),
		);

		// Allow other apps to add new notification types
		$additionalNotificationTypes = $this->activityManager->getNotificationTypes($l->getLanguageCode());
		$notificationTypes = array_merge($notificationTypes, $additionalNotificationTypes);

		$this->notificationTypes[$l->getLanguageCode()] = $notificationTypes;

		return $notificationTypes;
	}

	/**
	 * Send an event into the activity stream
	 *
	 * @param string $app The app where this event is associated with
	 * @param string $subject A short description of the event
	 * @param array  $subjectparams Array with parameters that are filled in the subject
	 * @param string $message A longer description of the event
	 * @param array  $messageparams Array with parameters that are filled in the message
	 * @param string $file The file including path where this event is associated with. (optional)
	 * @param string $link A link where this event is associated with (optional)
	 * @param string $affecteduser If empty the current user will be used
	 * @param string $type Type of the notification
	 * @param int    $prio Priority of the notification
	 * @return bool
	 */
	public static function send($app, $subject, $subjectparams = array(), $message = '', $messageparams = array(), $file = '', $link = '', $affecteduser = '', $type = '', $prio = Data::PRIORITY_MEDIUM) {
		$timestamp = time();
		$user = User::getUser();
		
		if ($affecteduser === '') {
			$auser = $user;
		} else {
			$auser = $affecteduser;
		}

		// store in DB
		$query = DB::prepare('INSERT INTO `*PREFIX*activity`(`app`, `subject`, `subjectparams`, `message`, `messageparams`, `file`, `link`, `user`, `affecteduser`, `timestamp`, `priority`, `type`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
		$query->execute(array($app, $subject, serialize($subjectparams), $message, serialize($messageparams), $file, $link, $user, $auser, $timestamp, $prio, $type));

		// fire a hook so that other apps like notification systems can connect
		Util::emitHook('OC_Activity', 'post_event', array('app' => $app, 'subject' => $subject, 'user' => $user, 'affecteduser' => $affecteduser, 'message' => $message, 'file' => $file, 'link'=> $link, 'prio' => $prio, 'type' => $type));

		return true;
	}

	/**
	 * @brief Send an event into the activity stream
	 *
	 * @param string $app The app where this event is associated with
	 * @param string $subject A short description of the event
	 * @param array  $subjectParams Array of parameters that are filled in the placeholders
	 * @param string $affectedUser Name of the user we are sending the activity to
	 * @param string $type Type of notification
	 * @param int $latestSendTime Activity time() + batch setting of $affecteduser
	 * @return bool
	 */
	public static function storeMail($app, $subject, array $subjectParams, $affectedUser, $type, $latestSendTime) {
		$timestamp = time();

		// store in DB
		$query = DB::prepare('INSERT INTO `*PREFIX*activity_mq` '
			. ' (`amq_appid`, `amq_subject`, `amq_subjectparams`, `amq_affecteduser`, `amq_timestamp`, `amq_type`, `amq_latest_send`) '
			. ' VALUES(?, ?, ?, ?, ?, ?, ?)');
		$query->execute(array(
			$app,
			$subject,
			serialize($subjectParams),
			$affectedUser,
			$timestamp,
			$type,
			$latestSendTime,
		));

		// fire a hook so that other apps like notification systems can connect
		Util::emitHook('OC_Activity', 'post_email', array(
			'app'			=> $app,
			'subject'		=> $subject,
			'subjectparams'	=> $subjectParams,
			'affecteduser'	=> $affectedUser,
			'timestamp'		=> $timestamp,
			'type'			=> $type,
			'latest_send'	=> $latestSendTime,
		));

		return true;
	}

	/**
	 * Filter the activity types
	 *
	 * @param array $types
	 * @param string $filter
	 * @return array
	 */
	public function filterNotificationTypes($types, $filter) {
		switch ($filter) {
			case 'shares':
				return array_intersect(array(
					Data::TYPE_SHARED,
				), $types);
		}

		// Allow other apps to add new notification types
		return $this->activityManager->filterNotificationTypes($types, $filter);
	}

	/**
	 * @brief Read a list of events from the activity stream
	 * @param GroupHelper $groupHelper Allows activities to be grouped
	 * @param int $start The start entry
	 * @param int $count The number of statements to read
	 * @param string $filter Filter the activities
	 * @return array
	 */
	public function read(GroupHelper $groupHelper, $start, $count, $filter = 'all') {
		// get current user
		$user = User::getUser();
		$enabledNotifications = UserSettings::getNotificationTypes($user, 'stream');
		$enabledNotifications = $this->filterNotificationTypes($enabledNotifications, $filter);

		// We don't want to display any activities
		if (empty($enabledNotifications)) {
			return array();
		}

		$parameters = array($user);
		$limitActivities = " AND `type` IN ('" . implode("','", $enabledNotifications) . "')";

		if ($filter === 'self') {
			$limitActivities .= ' AND `user` = ?';
			$parameters[] = $user;
		}
		else if ($filter === 'by') {
			$limitActivities .= ' AND `user` <> ?';
			$parameters[] = $user;
		}
		else if ($filter !== 'all') {
			switch ($filter) {
				case 'files':
					$limitActivities .= ' AND `app` = ?';
					$parameters[] = 'files';
				break;

				default:
					list($condition, $params) = $this->activityManager->getQueryForFilter($filter);
					if (!is_null($condition)) {
						$limitActivities .= ' ';
						$limitActivities .= $condition;
						if (is_array($params)) {
							$parameters = array_merge($parameters, $params);
						}
					}
			}
		}

		// fetch from DB
		$query = DB::prepare(
			'SELECT * '
			. ' FROM `*PREFIX*activity` '
			. ' WHERE `affecteduser` = ? ' . $limitActivities
			. ' ORDER BY `timestamp` DESC',
			$count, $start);
		$result = $query->execute($parameters);

		return $this->getActivitiesFromQueryResult($result, $groupHelper);
	}

	/**
	 * Process the result and return the activities
	 *
	 * @param \OC_DB_StatementWrapper|int $result
	 * @param \OCA\Activity\GroupHelper $groupHelper
	 * @return array
	 */
	public function getActivitiesFromQueryResult($result, GroupHelper $groupHelper) {
		if (DB::isError($result)) {
			Util::writeLog('Activity', DB::getErrorMessage($result), Util::ERROR);
		} else {
			while ($row = $result->fetchRow()) {
				$groupHelper->addActivity($row);
			}
		}

		return $groupHelper->getActivities();
	}

	/**
	 * Get the casted page number from $_GET
	 * @return int
	 */
	public function getPageFromParam() {
		if (isset($_GET['page'])) {
			return (int) $_GET['page'];
		}

		return 1;
	}

	/**
	 * Get the filter from $_GET
	 * @return string
	 */
	public function getFilterFromParam() {
		if (!isset($_GET['filter']))
			return 'all';

		$filterValue = $_GET['filter'];
		switch ($filterValue) {
			case 'by':
			case 'self':
			case 'shares':
			case 'all':
			case 'files':
				return $filterValue;
			default:
				if ($this->activityManager->isFilterValid($filterValue)) {
					return $filterValue;
				}
				return 'all';
		}
	}

	/**
	 * Delete old events
	 *
	 * @param int $expireDays Minimum 1 day
	 * @return null
	 */
	public function expire($expireDays = 365) {
		$ttl = (60 * 60 * 24 * max(1, $expireDays));

		$timelimit = time() - $ttl;
		$this->deleteActivities(array(
			'timestamp' => array($timelimit, '<'),
		));
	}

	/**
	 * Delete activities that match certain conditions
	 *
	 * @param array $conditions Array with conditions that have to be met
	 *                      'field' => 'value'  => `field` = 'value'
	 *    'field' => array('value', 'operator') => `field` operator 'value'
	 * @return null
	 */
	public function deleteActivities($conditions) {
		$sqlWhere = '';
		$sqlParameters = $sqlWhereList = array();
		foreach ($conditions as $column => $comparison) {
			$sqlWhereList[] = " `$column` " . ((is_array($comparison) && isset($comparison[1])) ? $comparison[1] : '=') . ' ? ';
			$sqlParameters[] = (is_array($comparison)) ? $comparison[0] : $comparison;
		}

		if (!empty($sqlWhereList)) {
			$sqlWhere = ' WHERE ' . implode(' AND ', $sqlWhereList);
		}

		$query = DB::prepare(
			'DELETE FROM `*PREFIX*activity`' . $sqlWhere);
		$query->execute($sqlParameters);
	}
}
