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
*
*/

\OCP\JSON::checkLoggedIn();
\OCP\JSON::checkAppEnabled('activity');
\OCP\JSON::callCheck();

$l = \OCP\Util::getL10N('activity');

$token = $tokenUrl = '';
if ($_POST['enable'] === 'true') {
	// Check for collisions
	$token = \OCP\Util::generateRandomBytes();
	$preferences = new \OC\Preferences(\OC_DB::getConnection());
	$conflicts = $preferences->getUsersForValue('activity', 'rsstoken', $token);

	while (!empty($conflicts)) {
		$token = \OCP\Util::generateRandomBytes();
		$conflicts = $preferences->getUsersForValue('activity', 'rsstoken', $token);
	}
	$tokenUrl = \OC::$server->getURLGenerator()->getAbsoluteURL(
		\OC::$server->getURLGenerator()->linkToRoute('activity.rss', array('token' => $token))
	);
}

\OCP\Config::setUserValue(\OCP\User::getUser(), 'activity', 'rsstoken', $token);

\OCP\JSON::success(array('data' => array(
	'message' => $l->t('Your settings have been updated.'),
	'rsslink' => $tokenUrl,
)));
