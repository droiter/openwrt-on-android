<?php
/**
 * ownCloud - Activity App
 *
 * @author Thomas Müller
 * @copyright 2013 Thomas Müller deepdiver@owncloud.com
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

use OCP\Activity\IConsumer;

class Consumer implements IConsumer
{
	/**
	 * Send an event into the activity stream of a user
	 *
	 * @param string $app The app where this event is associated with
	 * @param string $subject A short description of the event
	 * @param array  $subjectParams Array with parameters that are filled in the subject
	 * @param string $message A longer description of the event
	 * @param array  $messageParams Array with parameters that are filled in the message
	 * @param string $file The file including path where this event is associated with. (optional)
	 * @param string $link A link where this event is associated with (optional)
	 * @param string $affectedUser If empty the current user will be used
	 * @param string $type Type of the notification
	 * @param int    $priority Priority of the notification
	 * @return null
	 */
	function receive($app, $subject, $subjectParams, $message, $messageParams, $file, $link, $affectedUser, $type, $priority) {
		Data::send($app, $subject, $subjectParams, $message, $messageParams, $file, $link, $affectedUser, $type, $priority);
	}
}
