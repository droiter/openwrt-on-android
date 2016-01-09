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
 * Class EmailNotification
 *
 * @package OCA\Activity\BackgroundJob
 */
class EmailNotification extends \OC\BackgroundJob\TimedJob {
	const CLI_EMAIL_BATCH_SIZE = 500;
	const WEB_EMAIL_BATCH_SIZE = 25;

	/** @var \OCA\Activity\MailQueueHandler */
	protected $mqHandler;

	public function __construct() {
		// Run all 15 Minutes
		$this->setInterval(15 * 60);
		$this->mqHandler = new \OCA\Activity\MailQueueHandler();
	}

	protected function run($argument) {
		if (\OC::$CLI) {
			do {
				// If we are in CLI mode, we keep sending emails
				// until we are done.
				$emails_sent = $this->runStep(self::CLI_EMAIL_BATCH_SIZE);
			} while ($emails_sent === self::CLI_EMAIL_BATCH_SIZE);
		} else {
			// Only send 25 Emails in one go for web cron
			$this->runStep(self::WEB_EMAIL_BATCH_SIZE);
		}
	}

	/**
	 * Send an email to {$limit} users
	 *
	 * @param int $limit Number of users we want to send an email to
	 * @return int Number of users we sent an email to
	 */
	protected function runStep($limit) {
		// Get all users which should receive an email
		$affectedUsers = $this->mqHandler->getAffectedUsers($limit);
		if (empty($affectedUsers)) {
			// No users found to notify, mission abort
			return 0;
		}

		$preferences = new \OC\Preferences(\OC_DB::getConnection());
		$userLanguages = $preferences->getValueForUsers('core', 'lang', $affectedUsers);
		$userEmails = $preferences->getValueForUsers('settings', 'email', $affectedUsers);

		// Get all items for these users
		// We don't use time() but "time() - 1" here, so we don't run into
		// runtime issues and delete emails later, which were created in the
		// same second, but were not collected for the emails.
		$sendTime = time() - 1;
		$mailData = $this->mqHandler->getItemsForUsers($affectedUsers, $sendTime);

		// Send Email
		$default_lang = \OCP\Config::getSystemValue('default_language', 'en');
		foreach ($mailData as $user => $data) {
			if (!isset($userEmails[$user])) {
				// The user did not setup an email address
				// So we will not send an email :(
				continue;
			}

			$language = (isset($userLanguages[$user])) ? $userLanguages[$user] : $default_lang;
			$this->mqHandler->sendEmailToUser($user, $userEmails[$user], $language, $data);
		}

		// Delete all entries we dealt with
		$this->mqHandler->deleteSentItems($affectedUsers, $sendTime);

		return sizeof($affectedUsers);
	}
}
