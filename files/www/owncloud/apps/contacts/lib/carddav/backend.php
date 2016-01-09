<?php
/**
 * ownCloud - Addressbook
 *
 * @author Jakob Sack
 * @author Thomas Tanghus
 * @copyright 2011 Jakob Sack mail@jakobsack.de
 * @copyright 2012-2014 Thomas Tanghus (thomas@tanghus.net)
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

use OCA\Contacts;

/**
 * This class exchanges data between SabreDav and the Address book backends.
 *
 * Address book IDs are a combination of the backend name and the ID it has
 * in that backend. For your own address books it can be e.g 'local::1' for
 * an address book shared with you it could be 'shared::2' an so forth.
 */
class Backend extends \Sabre\CardDAV\Backend\AbstractBackend {

	public function __construct($backends) {
		$this->backends = $backends;
	}

	/**
	 * Returns the list of addressbooks for a specific user.
	 *
	 * @param string $principaluri
	 * @return array
	 */
	public function getAddressBooksForUser($principaluri) {

		$app = new Contacts\App();
		$userAddressBooks = array();
		foreach($this->backends as $backendName) {
			$backend = $app->getBackend($backendName);
			$addressBooks = $backend->getAddressBooksForUser();
			
			if (is_array($addressBooks)) {
				foreach($addressBooks as $addressBook) {
					if($addressBook['owner'] != \OCP\USER::getUser()) {
						$addressBook['uri'] = $addressBook['uri'] . '_shared_by_' . $addressBook['owner'];
						$addressBook['displayname'] = $addressBook['displayname'];
					}
					$userAddressBooks[] = array(
						'id'  => $backend->name . '::' . $addressBook['id'],
						'uri' => $addressBook['uri'],
						'principaluri' => 'principals/'.$addressBook['owner'],
						'{DAV:}displayname' => $addressBook['displayname'],
						'{' . \Sabre\CardDAV\Plugin::NS_CARDDAV . '}addressbook-description'
								=> $addressBook['description'],
						'{http://calendarserver.org/ns/}getctag' => $addressBook['lastmodified'],
						'{' . \Sabre\CardDAV\Plugin::NS_CARDDAV . '}supported-address-data' =>
							new \Sabre\CardDAV\Property\SupportedAddressData(),
					);
				}
			}
		}

		return $userAddressBooks;
	}


	/**
	 * Updates an addressbook's properties
	 *
	 * See \Sabre\DAV\IProperties for a description of the mutations array, as
	 * well as the return value.
	 *
	 * @param mixed $addressbookid
	 * @param array $mutations
	 * @see \Sabre\DAV\IProperties::updateProperties
	 * @return bool|array
	 */
	public function updateAddressBook($addressbookid, array $mutations) {
		$changes = array();

		foreach($mutations as $property=>$newvalue) {
			switch($property) {
				case '{DAV:}displayname' :
					$changes['displayname'] = $newvalue;
					break;
				case '{' . \Sabre\CardDAV\Plugin::NS_CARDDAV
						. '}addressbook-description' :
					$changes['description'] = $newvalue;
					break;
				default :
					// If any unsupported values were being updated, we must
					// let the entire request fail.
					return false;
			}
		}

		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		return $backend->updateAddressBook($id, $changes);

	}

	/**
	 * Creates a new address book
	 *
	 * @param string $principaluri
	 * @param string $uri Just the 'basename' of the url.
	 * @param array $properties
	 * @return void
	 */
	public function createAddressBook($principaluri, $uri, array $properties) {

		foreach($properties as $property => $newvalue) {

			switch($property) {
				case '{DAV:}displayname' :
					$properties['displayname'] = $newvalue;
					break;
				case '{' . \Sabre\CardDAV\Plugin::NS_CARDDAV
						. '}addressbook-description' :
					$properties['description'] = $newvalue;
					break;
				default :
					throw new \Sabre\DAV\Exception\BadRequest('Unknown property: '
						. $property);
			}

		}

		$properties['uri'] = $uri;

		$app = new Contacts\App();
		$backend = $app->getBackend('local');

		$backend->createAddressBook($properties);
	}

	/**
	 * Deletes an entire addressbook and all its contents
	 *
	 * @param mixed $addressbookid
	 * @return void
	 */
	public function deleteAddressBook($addressbookid) {
		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		$backend->deleteAddressBook($id);
	}

	/**
	 * Returns the last modified date if the backend supports it.
	 *
	 * @param mixed $addressbookid
	 * @return void
	 */
	public function lastModifiedAddressBook($addressbookid) {
		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		return $backend->lastModifiedAddressBook($id);
	}

	/**
	 * Returns all cards for a specific addressbook id.
	 *
	 * @param mixed $addressbookid
	 * @return array
	 */
	public function getCards($addressbookid) {
		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		$contacts = $backend->getContacts($id);

		$cards = array();
		foreach($contacts as $contact) {
			$cards[] = array(
				'id' => $contact['id'],
				//'carddata' => $i['carddata'],
				'size' => strlen($contact['carddata']),
				'etag' => '"' . md5($contact['carddata']) . '"',
				'uri' => urlencode($contact['uri']),
				'lastmodified' => $contact['lastmodified'] );
		}

		return $cards;
	}

	/**
	 * Returns a specfic card
	 *
	 * @param mixed $addressbookid
	 * @param string $carduri
	 * @return array
	 */
	public function getCard($addressbookid, $carduri) {
		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		try {
			$contact = $backend->getContact($id, array('uri' => urldecode($carduri)));
		} catch(\Exception $e) {
			//throw new \Sabre\DAV\Exception\NotFound($e->getMessage());
			\OCP\Util::writeLog('contacts', __METHOD__.', Exception: '. $e->getMessage(), \OCP\Util::DEBUG);
			return false;
		}
		if(is_array($contact) ) {
			$contact['etag'] = '"' . md5($contact['carddata']) . '"';
			return $contact;
		}
		//throw new \Sabre\DAV\Exception('Error retrieving the card');
		return false;
	}

	/**
	 * Creates a new card
	 *
	 * We don't return an Etag as the carddata can have been modified
	 * by Plugin::validate()
	 *
	 * @see Plugin::validate()
	 * @param mixed $addressbookid
	 * @param string $carduri
	 * @param string $carddata
	 * @return string|null
	 */
	public function createCard($addressbookid, $carduri, $carddata) {
		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		$backend->createContact($id, $carddata, array('uri' => $carduri));
	}

	/**
	 * Updates a card
	 *
	 * @param mixed $addressbookid
	 * @param string $carduri
	 * @param string $carddata
	 * @return null
	 */
	public function updateCard($addressbookid, $carduri, $carddata) {
		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		$backend->updateContact($id, array('uri' => $carduri,), $carddata);
	}

	/**
	 * Deletes a card
	 *
	 * @param mixed $addressbookid
	 * @param string $carduri
	 * @return bool
	 */
	public function deleteCard($addressbookid, $carduri) {
		list($id, $backend) = $this->getBackendForAddressBook($addressbookid);
		return $backend->deleteContact($id, array('uri' => $carduri));
	}

	/**
	 * @brief gets the userid from a principal path
	 * @param string $principaluri
	 * @return string
	 */
	public function userIDByPrincipal($principaluri) {
		list(, $userid) = \Sabre\DAV\URLUtil::splitPath($principaluri);
		return $userid;
	}

	/**
	 * Get the backend for an address book
	 *
	 * @param mixed $addressbookid
	 * @return array(string, \OCA\Contacts\Backend\AbstractBackend)
	 */
	public function getBackendForAddressBook($addressbookid) {
		list($backendName, $id) = explode('::', $addressbookid);
		$app = new Contacts\App();
		$backend = $app->getBackend($backendName);
		if($backend->name === $backendName && $backend->hasAddressBook($id)) {
			return array($id, $backend);
		}
		throw new \Sabre\DAV\Exception\NotFound('Backend not found: ' . $addressbookid);
	}
}
