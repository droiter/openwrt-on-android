<?php
/**
 * ownCloud - Backend for Shared contacts
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

namespace OCA\Contacts\Backend;

use OCA\Contacts;

/**
 * Backend class for shared address books.
 */
class Shared extends Database {

	/**
	 * The name of the backend.
	 *
	 * @var string
	 */
	public $name = 'shared';

	/**
	 * The cached address books.
	 *
	 * @var array[]
	 */
	public $addressBooks = array();

	/**
	* {@inheritdoc}
	*/
	public function getAddressBooksForUser(array $options = array()) {

		// workaround for https://github.com/owncloud/core/issues/2814
		$maybeSharedAddressBook = \OCP\Share::getItemsSharedWith(
			'addressbook',
			Contacts\Share\Addressbook::FORMAT_ADDRESSBOOKS
		);

		foreach ($maybeSharedAddressBook as $sharedAddressbook) {

			if (isset($sharedAddressbook['id'])) {
				$this->addressBooks[$sharedAddressbook['id']] = $sharedAddressbook;
				$this->addressBooks[$sharedAddressbook['id']]['backend'] = $this->name;
			}

		}

		return $this->addressBooks;
	}

	/**
	* {@inheritdoc}
	*/
	public function getAddressBook($addressBookId, array $options = array()) {

		foreach ($this->addressBooks as $addressBook) {

			if ($addressBook['id'] === $addressBookId) {
				return $addressBook;
			}

		}

		$addressBook = \OCP\Share::getItemSharedWithBySource(
			'addressbook',
			$addressBookId,
			Contacts\Share\Addressbook::FORMAT_ADDRESSBOOKS
		);

		// Not sure if I'm doing it wrongly, or if its supposed to return
		// the info in an array?
		$addressBook = (isset($addressBook['permissions']) ? $addressBook : $addressBook[0]);

		if(!isset($addressBook['permissions'])) {
			return null;
		}

		$addressBook['backend'] = $this->name;
		$this->addressBooks[] = $addressBook;
		return $addressBook;
	}

	/**
	* {@inheritdoc}
	*/
	public function getContacts($addressBookId, array $options = array()) {

		$addressBook = $this->getAddressBook($addressBookId);

		if (!$addressBook) {
			throw new \Exception('Shared Address Book not found: ' . $addressBookId, 404);
		}

		$permissions = $addressBook['permissions'];

		$cards = parent::getContacts($addressBookId, $options);

		foreach ($cards as &$card) {
			$card['permissions'] = $permissions;
		}

		return $cards;
	}

	/**
	* {@inheritdoc}
	*/
	public function getContact($addressBookId, $id, array $options = array()) {

		$addressBook = $this->getAddressBook($addressBookId);

		if (!$addressBook) {
			throw new \Exception('Shared Address Book not found: ' . $addressBookId, 404);
		}

		$permissions = $addressBook['permissions'];

		$card = parent::getContact($addressBookId, $id, $options);

		if (!$card) {
			throw new \Exception('Shared Contact not found: ' . implode(',', $id), 404);
		}

		$card['permissions'] = $permissions;
		return $card;
	}
}
