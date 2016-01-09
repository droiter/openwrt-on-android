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

/**
 * Class MailQueueHandler
 * Gets the users from the database and
 *
 * @package OCA\Activity
 */
class MailQueueHandler {
	/** @var array */
	protected $languages;

	/** @var string */
	protected $senderAddress;

	/** @var string */
	protected $senderName;

	/**
	 * Get the users we want to send an email to
	 *
	 * @param int|null $limit
	 * @return array
	 */
	public function getAffectedUsers($limit) {
		$limit = (!$limit) ? null : (int) $limit;

		$query = \OCP\DB::prepare(
			'SELECT `amq_affecteduser`, MIN(`amq_latest_send`) AS `amq_trigger_time` '
			. ' FROM `*PREFIX*activity_mq` '
			. ' WHERE `amq_latest_send` < ? '
			. ' GROUP BY `amq_affecteduser` '
			. ' ORDER BY `amq_trigger_time` ASC',
			$limit);
		$result = $query->execute(array(time()));

		$affectedUsers = array();
		if (\OCP\DB::isError($result)) {
			\OCP\Util::writeLog('OCA\Activity', \OCP\DB::getErrorMessage($result), \OCP\Util::ERROR);
		} else {
			while ($row = $result->fetchRow()) {
				$affectedUsers[] = $row['amq_affecteduser'];
			}
		}

		return $affectedUsers;
	}

	/**
	 * Get all items for the users we want to send an email to
	 *
	 * @param array $affectedUsers
	 * @param int $maxTime
	 * @return array Notification data (user => array of rows from the table)
	 */
	public function getItemsForUsers($affectedUsers, $maxTime) {
		$placeholders = implode(',', array_fill(0, sizeof($affectedUsers), '?'));
		$queryParams = $affectedUsers;
		array_unshift($queryParams, (int) $maxTime);

		$query = \OCP\DB::prepare(
			'SELECT * '
			. ' FROM `*PREFIX*activity_mq` '
			. ' WHERE `amq_timestamp` <= ? '
			. ' AND `amq_affecteduser` IN (' . $placeholders . ')'
			. ' ORDER BY `amq_timestamp` ASC'
		);
		$result = $query->execute($queryParams);

		$userActivityMap = array();
		if (\OCP\DB::isError($result)) {
			\OCP\Util::writeLog('Activity', \OCP\DB::getErrorMessage($result), \OCP\Util::ERROR);
		} else {
			while ($row = $result->fetchRow()) {
				$userActivityMap[$row['amq_affecteduser']][] = $row;
			}
		}

		return $userActivityMap;
	}

	/**
	 * Get a language object for a specific language
	 *
	 * @param string $lang Language identifier
	 * @return \OC_L10N Language object of $lang
	 */
	protected function getLanguage($lang) {
		if (!isset($this->languages[$lang])) {
			$this->languages[$lang] = \OC_L10N::get('activity', $lang);
		}

		return $this->languages[$lang];
	}

	/**
	 * Get the sender data
	 * @param string $setting Either `email` or `name`
	 * @return string
	 */
	protected function getSenderData($setting) {
		if (empty($this->senderAddress)) {
			$this->senderAddress = \OCP\Util::getDefaultEmailAddress('no-reply');
		}
		if (empty($this->senderName)) {
			$defaults = new \OCP\Defaults();
			$this->senderName = $defaults->getName();
		}

		if ($setting === 'email') {
			return $this->senderAddress;
		}
		return $this->senderName;
	}

	/**
	 * Get the selected setting for a time frame
	 * However we are not very accurate here, so we match the setting of the user
	 * a bit better.
	 *
	 * @param int $timestamp
	 * @return int Email send option that is used.
	 */
	protected function getLangForApproximatedTimeFrame($timestamp) {
		if (time() - $timestamp < 4000) {
			return UserSettings::EMAIL_SEND_HOURLY;
		} else if (time() - $timestamp < 90000) {
			return UserSettings::EMAIL_SEND_DAILY;
		} else {
			return UserSettings::EMAIL_SEND_WEEKLY;
		}
	}

	/**
	 * Send a notification to one user
	 *
	 * @param string $user Username of the recipient
	 * @param string $email Email address of the recipient
	 * @param string $lang Selected language of the recipient
	 * @param array $mailData Notification data we send to the user
	 */
	public function sendEmailToUser($user, $email, $lang, $mailData) {
		$l = $this->getLanguage($lang);
		$dataHelper = new DataHelper(
			\OC::$server->getActivityManager(),
			new ParameterHelper(
				new \OC\Files\View(''),
				\OC::$server->getConfig(),
				$l
			),
			$l
		);

		$activityList = array();
		foreach ($mailData as $activity) {
			$activityList[] = array(
				$dataHelper->translation(
					$activity['amq_appid'], $activity['amq_subject'], unserialize($activity['amq_subjectparams'])
				),
				$this->generateRelativeDatetime($l, $activity['amq_timestamp']),
			);
		}

		$alttext = new \OCP\Template('activity', 'email.notification', '');
		$alttext->assign('username', \OCP\User::getDisplayName($user));
		$alttext->assign('timeframe', $this->getLangForApproximatedTimeFrame($mailData[0]['amq_timestamp']));
		$alttext->assign('activities', $activityList);
		$alttext->assign('owncloud_installation', \OC_Helper::makeURLAbsolute('/'));
		$alttext->assign('overwriteL10N', $l);
		$emailText = $alttext->fetchPage();

		try {
			\OCP\Util::sendMail(
				$email, \OCP\User::getDisplayName($user),
				$l->t('Activity notification'), $emailText,
				$this->getSenderData('email'), $this->getSenderData('name')
			);
		} catch (\Exception $e) {
			\OCP\Util::writeLog('Activity', 'A problem occurred while sending the e-mail. Please revisit your settings.', \OCP\Util::ERROR);
		}
	}

	/**
	 * Creates a relative datetime string (with today, yesterday) or the normal date
	 *
	 * @param \OC_L10N $l
	 * @param $timestamp
	 * @return string
	 */
	protected function generateRelativeDatetime(\OC_L10N $l, $timestamp) {
		$dateOfTimestamp = $l->l('date', $timestamp);
		$dateOfToday = $l->l('date', time());
		$dateOfYesterday = $l->l('date', time() - 3600 * 24);

		if ($dateOfTimestamp === $dateOfToday) {
			return (string) $l->t('Today %s', $l->l('time', $timestamp));
		}

		if ($dateOfTimestamp === $dateOfYesterday) {
			return (string) $l->t('Yesterday %s', $l->l('time', $timestamp));
		}

		return (string) $l->l('datetime', $timestamp);
	}

	/**
	 * Delete all entries we dealt with
	 *
	 * @param array $affectedUsers
	 * @param int $maxTime
	 */
	public function deleteSentItems($affectedUsers, $maxTime) {
		$placeholders = implode(',', array_fill(0, sizeof($affectedUsers), '?'));
		$queryParams = $affectedUsers;
		array_unshift($queryParams, (int) $maxTime);

		$query = \OCP\DB::prepare(
			'DELETE FROM `*PREFIX*activity_mq` '
			. ' WHERE `amq_timestamp` <= ? '
			. ' AND `amq_affecteduser` IN (' . $placeholders . ')');
		$result = $query->execute($queryParams);

		if (\OCP\DB::isError($result)) {
			\OCP\Util::writeLog('Activity', \OCP\DB::getErrorMessage($result), \OCP\Util::ERROR);
		}
	}
}
