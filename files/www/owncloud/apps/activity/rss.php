<?php

/**
 * ownCloud - Activity app
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

\OCP\App::checkAppEnabled('activity');

$forceUserLogout = false;
if (!\OCP\User::isLoggedIn()) {
	if (!isset($_GET['token']) || strlen($_GET['token']) !== 30) {
		// Token missing or invalid
		header('HTTP/1.0 404 Not Found');
		exit;
	}

	$preferences = new \OC\Preferences(\OC_DB::getConnection());
	$users = $preferences->getUsersForValue('activity', 'rsstoken', $_GET['token']);

	if (sizeof($users) !== 1) {
		// User not found
		header('HTTP/1.0 404 Not Found');
		exit;
	}

	// Token found login as that user
	\OC_User::setUserId(array_shift($users));
	$forceUserLogout = true;
}

// check if the user has the right permissions.
\OCP\User::checkLoggedIn();

// rss is of content type text/xml
if (isset($_SERVER['HTTP_ACCEPT']) && stristr($_SERVER['HTTP_ACCEPT'], 'application/rss+xml')) {
	header('Content-Type: application/rss+xml');
} else {
	header('Content-Type: text/xml; charset=UTF-8');
}

// generate and show the rss feed
$l = \OCP\Util::getL10N('activity');
$data = new \OCA\Activity\Data(\OC::$server->getActivityManager());
$groupHelper = new \OCA\Activity\GroupHelper(
	\OC::$server->getActivityManager(),
	new \OCA\Activity\DataHelper(
		\OC::$server->getActivityManager(),
		new \OCA\Activity\ParameterHelper(
			new \OC\Files\View(''),
			\OC::$server->getConfig(),
			$l
		),
		$l
	),
	false
);

$tmpl = new \OCP\Template('activity', 'rss');

$tmpl->assign('rssLang', \OC_Preferences::getValue(\OCP\User::getUser(), 'core', 'lang'));
$tmpl->assign('rssLink', \OC::$server->getURLGenerator()->getAbsoluteURL(
	\OC::$server->getURLGenerator()->linkToRoute('activity.rss')
));
$tmpl->assign('rssPubDate', date('r'));
$tmpl->assign('user', \OCP\User::getUser());

$tmpl->assign('activities', $data->read($groupHelper, 0, 30, 'all'));

$tmpl->printPage();

if ($forceUserLogout) {
	\OCP\User::logout();
}
