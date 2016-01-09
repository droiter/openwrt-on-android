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

/** @var $this OC\Route\Router */

$this->create('activity.index', '')
	->actionInclude('activity/index.php');
$this->create('activity.ajax.fetch', 'ajax/fetch.php')
	->actionInclude('activity/ajax/fetch.php');
$this->create('activity.ajax.rssfeed', 'ajax/rssfeed.php')
	->actionInclude('activity/ajax/rssfeed.php');
$this->create('activity.ajax.settings', 'ajax/settings.php')
	->actionInclude('activity/ajax/settings.php');
$this->create('activity.rss', 'rss.php')
	->actionInclude('activity/rss.php');

// Register an OCS API call
OC_API::register(
	'get',
	'/cloud/activity',
	array('OCA\Activity\Api', 'get'),
	'activity'
);
