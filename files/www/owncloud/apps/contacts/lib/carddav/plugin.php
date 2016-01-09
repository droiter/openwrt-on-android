<?php
/**
 * ownCloud - CardDAV plugin
 *
 * The CardDAV plugin adds CardDAV functionality to the WebDAV server
 *
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
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

namespace OCA\Contacts\CardDAV;

use Sabre\VObject;
use OCA\Contacts\VObject\VCard;

/**
 * This class overrides \Sabre\CardDAV\Plugin::validateVCard() to be able
 * to import partially invalid vCards by ignoring invalid lines and to
 * validate and upgrade using \OCA\Contacts\VCard.
*/
class Plugin extends \Sabre\CardDAV\Plugin {

	/**
	* Checks if the submitted vCard data is in fact, valid.
	*
	* An exception is thrown if it's not.
	*
	* @param resource|string $data
	* @return void
	*/
	protected function validateVCard(&$data) {

		// If it's a stream, we convert it to a string first.
		if (is_resource($data)) {
			$data = stream_get_contents($data);
		} elseif (!is_string($data)) {
			throw new \Exception(__METHOD__ . ' argument 1 only supports string or stream resource.');
		}

		try {
			$vobj = VObject\Reader::read($data, VObject\Reader::OPTION_IGNORE_INVALID_LINES);
		} catch (VObject\ParseException $e) {
			throw new \Sabre\DAV\Exception\UnsupportedMediaType('This resource only supports valid vcard data. Parse error: ' . $e->getMessage());
		}

		if ($vobj->name !== 'VCARD') {
			throw new \Sabre\DAV\Exception\UnsupportedMediaType('This collection can only support vcard objects.');
		}

		$vobj->validate(VCard::REPAIR|VCard::UPGRADE);
		$data = $vobj->serialize();
	}
}
