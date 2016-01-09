<?php
/**
 * ownCloud - Database backend for Contacts
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

use OCA\Contacts\Contact,
	OCA\Contacts\VObject\VCard,
	OCA\Contacts\Utils\Properties,
	Sabre\VObject\Reader;

/**
 * Backend class for a users own contacts.
 */

class Database extends AbstractBackend {

	static private $preparedQueries = array();

	/**
	 * The name of the backend.
	 *
	 * @var string
	 */
	public $name = 'local';

	/**
	 * The cached address books.
	 *
	 * @var array[]
	 */
	public $addressBooks;

	/**
	 * The table that holds the address books.
	 *
	 * @var string
	 */
	public $addressBooksTableName;

	/**
	 * The table that holds the contact vCards.
	 *
	 * @var string
	 */
	public $cardsTableName;

	/**
	 * The table that holds the indexed vCard properties.
	 *
	 * @var string
	 */
	public $indexTableName;

	/**
	* Sets up the backend
	*
	* @param string $addressBooksTableName
	* @param string $cardsTableName
	*/
	public function __construct(
		$userid = null,
		$options = array(
			'addressBooksTableName' => '*PREFIX*contacts_addressbooks',
			'cardsTableName' => '*PREFIX*contacts_cards',
			'indexTableName' => '*PREFIX*contacts_cards_properties'
		)
	) {
		$this->userid = $userid ? $userid : \OCP\User::getUser();
		$this->addressBooksTableName = $options['addressBooksTableName'];
		$this->cardsTableName = $options['cardsTableName'];
		$this->indexTableName = $options['indexTableName'];
		$this->addressBooks = array();
	}

	/**
	* {@inheritdoc}
	*/
	public function getAddressBooksForUser(array $options = array()) {

		try {
			$result = $this->getPreparedQuery('getaddressbooksforuser')
				->execute(array($this->userid));

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: '
					. \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return $this->addressBooks;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.' exception: '
				. $e->getMessage(), \OCP\Util::ERROR);
			return $this->addressBooks;
		}

		while ($row = $result->fetchRow()) {
			$row['permissions'] = \OCP\PERMISSION_ALL;
			$this->addressBooks[$row['id']] = $row;
		}

		return $this->addressBooks;
	}

	/**
	* {@inheritdoc}
	*/
	public function getAddressBook($addressBookId, array $options = array()) {
		$owner = isset($options['shared_by']) ? $options['shared_by'] : $this->userid;
		//\OCP\Util::writeLog('contacts', __METHOD__.' id: '
		//	. $addressBookId, \OCP\Util::DEBUG);
		if ($this->addressBooks && isset($this->addressBooks[$addressBookId])) {
			//print(__METHOD__ . ' ' . __LINE__ .' addressBookInfo: ' . print_r($this->addressBooks[$addressBookId], true));
			return $this->addressBooks[$addressBookId];
		}

		// Hmm, not found. Lets query the db.
		try {
			$result = $this->getPreparedQuery('getaddressbook')->execute(array($addressBookId, $owner));

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: '
					. \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return null;
			}

			$row = $result->fetchRow();

			if (!$row) {
				return null;
			}

			$row['permissions'] = \OCP\PERMISSION_ALL;
			$row['backend'] = $this->name;
			$this->addressBooks[$addressBookId] = $row;
			return $row;

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.' exception: '
				. $e->getMessage(), \OCP\Util::ERROR);
			return null;
		}

	}

	/**
	* {@inheritdoc}
	*/
	public function hasAddressBook($addressBookId) {

		// First check if it's already cached
		if ($this->addressBooks && isset($this->addressBooks[$addressBookId])) {
			return true;
		}

		return count($this->getAddressBook($addressBookId)) > 0;
	}

	/**
	 * Updates an addressbook's properties
	 *
	 * @param string $addressBookId
	 * @param array $changes
	 * @return bool
	 */
	public function updateAddressBook($addressBookId, array $changes) {

		if (count($changes) === 0) {
			return false;
		}

		$query = 'UPDATE `' . $this->addressBooksTableName . '` SET ';

		$updates = array();

		if (isset($changes['displayname'])) {
			$query .= '`displayname` = ?, ';
			$updates[] = $changes['displayname'];

			if ($this->addressBooks && isset($this->addressBooks[$addressBookId])) {
				$this->addressBooks[$addressBookId]['displayname'] = $changes['displayname'];
			}

		}

		if (isset($changes['description'])) {

			$query .= '`description` = ?, ';
			$updates[] = $changes['description'];

			if ($this->addressBooks && isset($this->addressBooks[$addressBookId])) {
				$this->addressBooks[$addressBookId]['description'] = $changes['description'];
			}

		}

		$query .= '`ctag` = ? + 1 WHERE `id` = ?';
		$now = time();
		$updates[] = $now;
		$updates[] = $addressBookId;

		if ($this->addressBooks && isset($this->addressBooks[$addressBookId])) {
			$this->addressBooks[$addressBookId]['lastmodified'] = $now;
		}

		try {

			$stmt = \OCP\DB::prepare($query);
			$result = $stmt->execute($updates);

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts',
					__METHOD__. 'DB error: '
					. \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return false;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts',
				__METHOD__ . ', exception: '
				. $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}

		return true;
	}

	/**
	 * Creates a new address book
	 *
	 * Supported properties are 'displayname', 'description' and 'uri'.
	 * 'uri' is supported to allow to add from CardDAV requests, and MUST
	 * be used for the 'uri' database field if present.
	 * 'displayname' MUST be present.
	 *
	 * @param array $properties
	 * @param array $options - Optional (backend specific options)
	 * @return string|false The ID if the newly created AddressBook or false on error.
	 */
	public function createAddressBook(array $properties) {

		if (count($properties) === 0 || !isset($properties['displayname'])) {
			return false;
		}

		$updates = array($this->userid, $properties['displayname']);
		$updates[] = isset($properties['uri'])
			? $properties['uri']
			: $this->createAddressBookURI($properties['displayname']);
		$updates[] = isset($properties['description']) ? $properties['description'] : '';
		$ctag = time();
		$updates[] = $ctag;

		try {
			$result = $this->getPreparedQuery('createaddressbook')->execute($updates);

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return false;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__ . ', exception: ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}

		$newid = \OCP\DB::insertid($this->addressBooksTableName);

		if ($this->addressBooks) {
			$updates['id'] = $newid;
			$updates['ctag'] = $ctag;
			$updates['lastmodified'] = $ctag;
			$updates['permissions'] = \OCP\PERMISSION_ALL;
			$this->addressBooks[$newid] = $updates;
		}

		return $newid;
	}

	/**
	 * Get all contact ids from the address book to run pre_deleteAddressBook hook
	 *
	 * @param string $addressBookId
	 */
	protected function preDeleteAddressBook($addressBookId) {
		// Get all contact ids for this address book
		$ids = array();
		$result = null;

		try {

			$result = $this->getPreparedQuery('getcontactids')
				->execute(array($addressBookId));

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: '
					. \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return false;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.
				', exception: ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}

		if (!is_null($result)) {
			while ($id = $result->fetchOne()) {
				$ids[] = $id;
			}

			\OCP\Util::emitHook('OCA\Contacts', 'pre_deleteAddressBook',
				array('addressbookid' => $addressBookId, 'contactids' => $ids)
			);
		}
	}

	/**
	 * Deletes an entire addressbook and all its contents
	 *
	 * NOTE: For efficience this method bypasses the cleanup hooks and deletes
	 * property indexes and category/group relations by itself.
	 *
	 * @param string $addressBookId
	 * @param array $options - Optional (backend specific options)
	 * @return bool
	 */
	public function deleteAddressBook($addressBookId) {

		$this->preDeleteAddressBook($addressBookId);

		try {
			$this->getPreparedQuery('deleteaddressbookcontacts')
				->execute(array($addressBookId));
		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.
				', exception: ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}

		try {
			$this->getPreparedQuery('deleteaddressbook')
				->execute(array($addressBookId));
		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.
				', exception: ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}

		if ($this->addressBooks && isset($this->addressBooks[$addressBookId])) {
			unset($this->addressBooks[$addressBookId]);
		}

		return true;
	}

	/**
	 * @brief Updates ctag for addressbook
	 * @param integer $id
	 * @return boolean
	 */
	public function setModifiedAddressBook($id) {
		$ctag = time();
		$this->getPreparedQuery('touchaddressbook')->execute(array($ctag, $id));

		return true;
	}

	/**
	* {@inheritdoc}
	*/
	public function lastModifiedAddressBook($addressBookId) {

		if ($this->addressBooks && isset($this->addressBooks[$addressBookId])) {
			return $this->addressBooks[$addressBookId]['lastmodified'];
		}

		$addressBook = $this->getAddressBook($addressBookId);
		if($addressBook) {
			$this->addressBooks[$addressBookId] = $addressBook;
		}
		return $addressBook ? $addressBook['lastmodified'] : null;
	}

	/**
	 * Returns the number of contacts in a specific address book.
	 *
	 * @param string $addressBookId
	 * @param bool $omitdata Don't fetch the entire carddata or vcard.
	 * @return array
	 */
	public function numContacts($addressBookId) {

		$result = $this->getPreparedQuery('numcontacts')->execute(array($addressBookId));

		if (\OCP\DB::isError($result)) {
			\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
			return null;
		}

		return (int)$result->fetchOne();
	}

	/**
	* {@inheritdoc}
	*/
	public function getContacts($addressBookId, array $options = array()) {
		//\OCP\Util::writeLog('contacts', __METHOD__.' addressbookid: ' . $addressBookId, \OCP\Util::DEBUG);
		$cards = array();
		try {
			$queryIdentifier = (isset($options['omitdata']) && $options['omitdata'] === true)
				? 'getcontactsomitdata'
				: 'getcontacts';

			$result = $this->getPreparedQuery($queryIdentifier, $options)->execute(array($addressBookId));

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return $cards;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
			return $cards;
		}

		if (!is_null($result)) {

			while ($row = $result->fetchRow()) {
				$row['permissions'] = \OCP\PERMISSION_ALL;
				$cards[] = $row;
			}

		}
		
		return $cards;
	}

	/**
	 * Returns a specific contact.
	 *
	 * NOTE: The contact $id for Database and Shared backends can be an array containing
	 * either 'id' or 'uri' to be able to play seamlessly with the
	 * CardDAV backend.
	 * NOTE: $addressbookid isn't always used in the query, so there's no access control.
	 * 	This is because the groups backend - \OCP\Tags - doesn't no about parent collections
	 * 	only object IDs. Hence a hack is made with an optional 'noCollection'.
	 *
	 * @param string $addressBookId
	 * @param string|array $id Contact ID
	 * @param array $options - Optional (backend specific options)
	 * @return array|null
	 */
	public function getContact($addressBookId, $id, array $options = array()) {
		//\OCP\Util::writeLog('contacts', __METHOD__.' identifier: ' . $addressBookId . ' / ' . $id, \OCP\Util::DEBUG);

		// When dealing with tags we have no idea if which address book it's in
		// but since they're all in the same table they have unique IDs anyway
		$noCollection = isset($options['noCollection']) ? $options['noCollection'] : false;

		$queryIdentifier = 'getcontact';
		$queries = array();

		// When querying from CardDAV we don't have the ID, only the uri
		if (is_array($id)) {
			if (isset($id['id'])) {
				$queries[] = $id['id'];
				$queryIdentifier .= 'byid';
			} elseif (isset($id['uri'])) {
				$queries[] = $id['uri'];
				$queryIdentifier .= 'byuri';
			} else {
				throw new \Exception(
					__METHOD__ . ' If second argument is an array, either \'id\' or \'uri\' has to be set.'
				);
			}
		} else {
			if (!trim($id)) {
				throw new \Exception(
					__METHOD__ . ' Missing or empty second argument \'$id\'.'
				);
			}
			$queries[] = $id;
			$queryIdentifier .= 'byid';
		}

		if ($noCollection) {
			$queryIdentifier .= 'nocollection';
		} else {
			$queries[] = $addressBookId;
		}

		try {
			//\OCP\Util::writeLog('contacts', __METHOD__.', identifier: '. $queryIdentifier . ', queries: ' . implode(',', $queries), \OCP\Util::DEBUG);
			$result = $this->getPreparedQuery($queryIdentifier)->execute($queries);

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return null;
			}

		} catch (\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
			\OCP\Util::writeLog('contacts', __METHOD__.', id: '. $id, \OCP\Util::DEBUG);
			return null;
		}

		$row = $result->fetchRow();

		if (!$row) {
			if (is_array($id)) {
				$idstr = implode(", ", $id);
			} else {
				$idstr = $id;
			}
			\OCP\Util::writeLog('contacts', __METHOD__.', Not found, id: '. $idstr, \OCP\Util::DEBUG);
			return null;
		}

		$row['permissions'] = \OCP\PERMISSION_ALL;
		return $row;
	}

	public function hasContact($addressBookId, $id) {
		try {
			return $this->getContact($addressBookId, $id) !== null;
		} catch (\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
			return false;
		}
	}

	/**
	 * Creates a new contact
	 *
	 * In the Database and Shared backends contact be either a Contact object or a string
	 * with carddata to be able to play seamlessly with the CardDAV backend.
	 * If this method is called by the CardDAV backend, the carddata is already validated.
	 * NOTE: It's assumed that this method is called either from the CardDAV backend, the
	 * import script, or from the ownCloud web UI in which case either the uri parameter is
	 * set, or the contact has a UID. If neither is set, it will fail.
	 *
	 * @param string $addressBookId
	 * @param VCard|string $contact
	 * @param array $options - Optional (backend specific options)
	 * @return string|bool The identifier for the new contact or false on error.
	 */
	public function createContact($addressBookId, $contact, array $options = array()) {
		//\OCP\Util::writeLog('contacts', __METHOD__.' addressBookId: ' . $addressBookId, \OCP\Util::DEBUG);

		$uri = isset($options['uri']) ? $options['uri'] : null;

		if (!$contact instanceof VCard) {
			try {
				$contact = Reader::read($contact);
			} catch(\Exception $e) {
				\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
				return false;
			}
		}

		try {
			$contact->validate(VCard::REPAIR|VCard::UPGRADE);
		} catch (\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__ . ' ' .
				'Error validating vcard: ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}

		$uri = is_null($uri) ? $this->uniqueURI($addressBookId, $contact->UID . '.vcf') : $uri;
		$now = new \DateTime;
		$contact->REV = $now->format(\DateTime::W3C);

		$appinfo = \OCP\App::getAppInfo('contacts');
		$appversion = \OCP\App::getAppVersion('contacts');
		$prodid = '-//ownCloud//NONSGML ' . $appinfo['name'] . ' ' . $appversion.'//EN';
		$contact->PRODID = $prodid;

		try {
			$result = $this->getPreparedQuery('createcontact')
				->execute(
					array(
						$addressBookId,
						(string)$contact->FN,
						$contact->serialize(),
						$uri,
						time()
					)
				);

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return false;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
			return false;
		}
		$newid = \OCP\DB::insertid($this->cardsTableName);

		$this->setModifiedAddressBook($addressBookId);
		\OCP\Util::emitHook('OCA\Contacts', 'post_createContact',
			array('id' => $newid, 'parent' => $addressBookId, 'backend' => $this->name, 'contact' => $contact)
		);
		return (string)$newid;
	}

	/**
	 * Updates a contact
	 *
	 * @param string $addressBookId
	 * @param string|array $id Contact ID
	 * @param VCard|string $contact
	 * @param array $options - Optional (backend specific options)
	 * @see getContact
	 * @return bool
	 * @throws \Exception if $contact is a string but can't be parsed as a VCard
	 * @throws \Exception if the Contact to update couldn't be found
	 */
	public function updateContact($addressBookId, $id, $contact, array $options = array()) {
		//\OCP\Util::writeLog('contacts', __METHOD__.' identifier: ' . $addressBookId . ' / ' . $id, \OCP\Util::DEBUG);
		$noCollection = isset($options['noCollection']) ? $options['noCollection'] : false;
		$isBatch = isset($options['isBatch']) ? $options['isBatch'] : false;

		$updateRevision = true;
		$isCardDAV = false;

		if (!$contact instanceof VCard) {
			try {
				$contact = Reader::read($contact);
			} catch(\Exception $e) {
				\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
				return false;
			}
		}

		if (is_array($id)) {

			if (isset($id['id'])) {
				$id = $id['id'];
			} elseif (isset($id['uri'])) {
				$updateRevision = false;
				$isCardDAV = true;
				$id = $this->getIdFromUri($id['uri']);

				if (is_null($id)) {
					\OCP\Util::writeLog('contacts', __METHOD__ . ' Couldn\'t find contact', \OCP\Util::ERROR);
					return false;
				}

			} else {
				throw new \Exception(
					__METHOD__ . ' If second argument is an array, either \'id\' or \'uri\' has to be set.'
				);
			}
		}

		if ($updateRevision || !isset($contact->REV)) {
			$now = new \DateTime;
			$contact->REV = $now->format(\DateTime::W3C);
		}

		$data = $contact->serialize();

		if ($noCollection) {
			$me = $this->getContact(null, $id, $options);
			$addressBookId = $me['parent'];
		}

		$updates = array($contact->FN, $data, time(), $id, $addressBookId);

		try {

			$result = $this->getPreparedQuery('updatecontact')->execute($updates);

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return false;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.', exception: '
				. $e->getMessage(), \OCP\Util::ERROR);
			\OCP\Util::writeLog('contacts', __METHOD__.', id' . $id, \OCP\Util::DEBUG);
			return false;
		}

		$this->setModifiedAddressBook($addressBookId);

		if (!$isBatch) {

			\OCP\Util::emitHook('OCA\Contacts', 'post_updateContact',
				array(
					'backend' => $this->name,
					'addressBookId' => $addressBookId,
					'contactId' => $id,
					'contact' => $contact,
					'carddav' => $isCardDAV
				)
			);

		}

		return true;
	}

	/**
	 * Deletes a contact
	 *
	 * @param string $addressBookId
	 * @param string|array $id
	 * @param array $options - Optional (backend specific options)
	 * @see getContact
	 * @return bool
	 */
	public function deleteContact($addressBookId, $id, array $options = array()) {
		// TODO: pass the uri in $options instead.

		$noCollection = isset($options['noCollection']) ? $options['noCollection'] : false;
		$isBatch = isset($options['isBatch']) ? $options['isBatch'] : false;

		if (is_array($id)) {

			if (isset($id['id'])) {
				$id = $id['id'];
			} elseif (isset($id['uri'])) {

				$id = $this->getIdFromUri($id['uri']);

				if (is_null($id)) {
					\OCP\Util::writeLog('contacts', __METHOD__ . ' Couldn\'t find contact', \OCP\Util::ERROR);
					return false;
				}

			} else {
				throw new \Exception(
					__METHOD__ . ' If second argument is an array, either \'id\' or \'uri\' has to be set.'
				);
			}
		}

		if (!$isBatch) {
			\OCP\Util::emitHook('OCA\Contacts', 'pre_deleteContact',
				array('id' => $id)
			);
		}

		if ($noCollection) {
			$me = $this->getContact(null, $id, $options);
			$addressBookId = $me['parent'];
		}

		try {

			$result = $this->getPreparedQuery('deletecontact')
				->execute(array($id, $addressBookId));

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: '
					. \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
				return false;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.
				', exception: ' . $e->getMessage(), \OCP\Util::ERROR);
			\OCP\Util::writeLog('contacts', __METHOD__.', id: '
				. $id, \OCP\Util::DEBUG);
			return false;
		}

		$this->setModifiedAddressBook($addressBookId);
		return true;
	}

	/**
	 * @brief Get the last modification time for a contact.
	 *
	 * Must return a UNIX time stamp or null if the backend
	 * doesn't support it.
	 *
	 * @param string $addressBookId
	 * @param mixed $id
	 * @returns int | null
	 */
	public function lastModifiedContact($addressBookId, $id) {

		$contact = $this->getContact($addressBookId, $id);
		return ($contact ? $contact['lastmodified'] : null);

	}

	/**
	 * @brief Get the contact id from the uri.
	 *
	 * @param mixed $id
	 * @returns int | null
	 */
	public function getIdFromUri($uri) {

		$stmt = $this->getPreparedQuery('contactidfromuri');
		$result = $stmt->execute(array($uri));

		if (\OCP\DB::isError($result)) {
			\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
			return null;
		}

		$one = $result->fetchOne();

		if (!$one) {
			\OCP\Util::writeLog('contacts', __METHOD__.', Not found, uri: '. $uri, \OCP\Util::DEBUG);
			return null;
		}

		return $one;
	}

	/**
	 * Create a unique URI based on the display name.
	 *
	 * @param string $displayName
	 * @return string
	 */
	private function createAddressBookURI($displayName) {

		$name = str_replace(' ', '_', strtolower($displayName));

		try {
			$stmt = $this->getPreparedQuery('addressbookuris');
			$result = $stmt->execute(array($this->userid));

			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts',
					__METHOD__. 'DB error: ' . \OC_DB::getErrorMessage($result),
					\OCP\Util::ERROR
				);
				return $name;
			}

		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__ . ' exception: ' . $e->getMessage(), \OCP\Util::ERROR);
			return $name;
		}
		$uris = array();
		while ($row = $result->fetchRow()) {
			$uris[] = $row['uri'];
		}

		$newname = $name;
		$i = 1
		;
		while (in_array($newname, $uris)) {
			$newname = $name.$i;
			$i = $i + 1;
		}
		return $newname;
	}

	/**
	* @brief Checks if a contact with the same URI already exist in the address book.
	* @param string $addressBookId Address book ID.
	* @param string $uri
	* @returns string Unique URI
	*/
	protected function uniqueURI($addressBookId, $uri) {
		$stmt = $this->getPreparedQuery('counturi');

		$result = $stmt->execute(array($addressBookId, $uri));
		$result = $result->fetchRow();

		if (is_array($result) && count($result) > 0 && $result['count'] > 0) {

			while (true) {
				$uri = Properties::generateUID() . '.vcf';
				$result = $stmt->execute(array($addressBookId, $uri));

				if (is_array($result) && count($result) > 0 && $result['count'] > 0) {
					continue;
				} else {
					return $uri;
				}

			}
		}

		return $uri;
	}

	/**
	* Collect (nearly) all queries in one place
	*
	* @param string $identifier
	* @param array $options Can be used for e.g. offset/limit
	* @throws \Exception If $identifier isn't known
	* @return \OC_DB_StatementWrapper
	*/
	protected function getPreparedQuery($identifier, array $options = array()) {

		if (isset(self::$preparedQueries[$identifier])) {
			return self::$preparedQueries[$identifier];
		}

		$args = array();

		switch ($identifier) {

			case 'getaddressbooksforuser':
				$args[] = 'SELECT `id`, `displayname`, `description`, `ctag`'
					. ' AS `lastmodified`, `userid` AS `owner`, `uri` FROM `'
					. $this->addressBooksTableName
					. '` WHERE `userid` = ? ORDER BY `displayname`';
				break;
			case 'getaddressbook':
				$args[] = 'SELECT `id`, `displayname`, `description`, '
						. '`userid` AS `owner`, `ctag` AS `lastmodified`, `uri` FROM `'
						. $this->addressBooksTableName
						. '` WHERE `id` = ? AND `userid` = ?';
				break;
			case 'createaddressbook':
				$args[] = 'INSERT INTO `'
						. $this->addressBooksTableName . '` '
						. '(`userid`,`displayname`,`uri`,`description`,`ctag`) '
						. 'VALUES(?,?,?,?,?)';
				break;
			case 'deleteaddressbookcontacts':
				$args[] = 'DELETE FROM `' . $this->cardsTableName
						. '` WHERE `addressbookid` = ?';
				break;
			case 'deleteaddressbook':
				$args[] = 'DELETE FROM `'
						. $this->addressBooksTableName . '` WHERE `id` = ?';
				break;
			case 'touchaddressbook':
				$args[] = 'UPDATE `' . $this->addressBooksTableName
						. '` SET `ctag` = ? + 1 WHERE `id` = ?';
				break;
			case 'counturi':
				$args[] = 'SELECT COUNT(*) AS `count` FROM `'
						. $this->cardsTableName
						. '` WHERE `addressbookid` = ? AND `uri` = ?';
				break;
			case 'addressbookuris':
				$args[] = 'SELECT `uri` FROM `'
						. $this->addressBooksTableName . '` WHERE `userid` = ? ';
				break;
			case 'contactidfromuri':
				$args[] = 'SELECT `id` FROM `'
						. $this->cardsTableName
						. '` WHERE `uri` = ?';
				break;
			case 'deletecontact':
				$args[] = 'DELETE FROM `'
					. $this->cardsTableName
					. '` WHERE `id` = ? AND `addressbookid` = ?';
				break;
			case 'updatecontact':
				$args[] = 'UPDATE `' . $this->cardsTableName
						. '` SET `fullname` = ?,`carddata` = ?, `lastmodified` = ?'
						. ' WHERE `id` = ? AND `addressbookid` = ?';
				break;
			case 'createcontact':
				$args[] = 'INSERT INTO `'
					. $this->cardsTableName
					. '` (`addressbookid`,`fullname`,`carddata`,`uri`,`lastmodified`) '
					. ' VALUES(?,?,?,?,?)';
				break;
			case 'getcontactbyid':
				$args[] = 'SELECT `id`, `uri`, `carddata`, `lastmodified`, '
						. '`addressbookid` AS `parent`, `fullname` AS `displayname` FROM `'
						. $this->cardsTableName
						. '` WHERE `id` = ? AND `addressbookid` = ?';
				break;
			case 'getcontactbyuri':
				$args[] = 'SELECT `id`, `uri`, `carddata`, `lastmodified`, '
						. '`addressbookid` AS `parent`, `fullname` AS `displayname` FROM `'
						. $this->cardsTableName
						. '` WHERE `uri` = ? AND `addressbookid` = ?';
				break;
			case 'getcontactbyidnocollection':
				$args[] = 'SELECT `id`, `uri`, `carddata`, `lastmodified`, '
						. '`addressbookid` AS `parent`, `fullname` AS `displayname` FROM `'
						. $this->cardsTableName . '` WHERE `id` = ?';
				break;
			case 'getcontactbyurinocollection':
				$args[] = 'SELECT `id`, `uri`, `carddata`, `lastmodified`, '
						. '`addressbookid` AS `parent`, `fullname` AS `displayname` FROM `'
						. $this->cardsTableName . '` WHERE `uri` = ?';
				break;
			case 'getcontactids':
				$args[] = 'SELECT `id` FROM `'
						. $this->cardsTableName . '` WHERE `addressbookid` = ?';
				break;
			case 'getcontacts':
				$args[] = 'SELECT `id`, `uri`, `carddata`, `lastmodified`, '
						. '`addressbookid` AS `parent`, `fullname` AS `displayname` FROM `'
						. $this->cardsTableName . '` WHERE `addressbookid` = ?';
				$args[] = isset($options['limit']) ? $options['limit'] : null;
				$args[] = isset($options['offset']) ? $options['offset'] : null;
				break;
			case 'getcontactsomitdata':
				$args[] = 'SELECT `id`, `uri`, `lastmodified`, '
						. '`addressbookid` AS `parent`, `fullname` AS `displayname` FROM `'
						. $this->cardsTableName . '` WHERE `addressbookid` = ?';
				$args[] = isset($options['limit']) ? $options['limit'] : null;
				$args[] = isset($options['offset']) ? $options['offset'] : null;
				break;
			case 'numcontacts':
				$args[] = 'SELECT COUNT(*) AS `count` FROM `'
						. $this->cardsTableName . '` WHERE `addressbookid` = ?';
				break;
			default:
				throw new \Exception('Unknown query identifier: ' . $identifier);

		}

		self::$preparedQueries[$identifier] = call_user_func_array('\OCP\DB::prepare', $args);

		return self::$preparedQueries[$identifier];
	}

	public function getSearchProvider($addressbook) {
		return new \OCA\Contacts\AddressbookProvider($addressbook);
	}
	
}
