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

/**
 * The following signals are being emitted:
 *
 * OCA\Contacts\VCard::post_moveToAddressbook(array('aid' => $aid, 'id' => $id))
 * OCA\Contacts\VCard::pre_deleteVCard(array('aid' => $aid, 'id' => $id, 'uri' = $uri)); (NOTE: the values can be null depending on which method emits them)
 * OCA\Contacts\VCard::post_updateVCard($id)
 * OCA\Contacts\VCard::post_createVCard($newid)
 */

namespace OCA\Contacts;

use \Sabre\VObject;

/**
 * This class contains all hooks.
 */
class Hooks{
	/**
	 * @brief Add default Addressbook for a certain user
	 * @param paramters parameters from postCreateUser-Hook
	 * @return array
	 */
	public static function userCreated($parameters) {
		//Addressbook::addDefault($parameters['uid']);
		return true;
	}

	/**
	 * @brief Deletes all Addressbooks of a certain user
	 * @param paramters parameters from postDeleteUser-Hook
	 * @return array
	 */
	public static function userDeleted($parameters) {
		$backend = new Backend\Database($parameters['uid']);
		$addressBooks = $backend->getAddressBooksForUser();

		foreach($addressBooks as $addressBook) {
			// Purging of contact categories and and properties is done by backend.
			$backend->deleteAddressBook($addressBook['id']);
		}
	}

	/**
	* Delete any registred address books (Future)
	*/
	public static function addressBookDeletion($parameters) {
		// Clean up sharing
		\OCP\Share::unshareAll('addressbook', $parameters['addressbookid']);

		if(count($parameters['contactids'])) {
			// Remove contacts from groups
			$tagMgr = \OC::$server->getTagManager()->load('contact');
			$tagMgr->purgeObjects($parameters['contactids']);

			// Purge property indexes
			Utils\Properties::purgeIndexes($parameters['contactids']);
		}
	}

	/**
	 * A contact has been deleted and cleanup for property indexes and
	 * group/contact relations must be performed.
	 *
	 * NOTE: When deleting an entire address book the cleanup is done in the
	 * addressBookDeletion() hook. Any cleanup procedures most be implemented
	 * in both.
	 *
	 * @param array $parameters Currently only the id of the contact.
	 */
	public static function contactDeletion($parameters) {
		\OCP\Util::writeLog('contacts', __METHOD__.' id: '.print_r($parameters['id'], true), \OCP\Util::DEBUG);
		$ids = is_array($parameters['id']) ? $parameters['id'] : array($parameters['id']);
		$tagMgr = \OC::$server->getTagManager()->load('contact');
		$tagMgr->purgeObjects($ids);
		Utils\Properties::purgeIndexes($ids);

		// Contact sharing not implemented, but keep for future.
		//\OCP\Share::unshareAll('contact', $id);
	}

	public static function contactAdded($parameters) {
		\OCP\Util::writeLog('contacts', __METHOD__.' id: '.$parameters['id'], \OCP\Util::DEBUG);
		$contact = $parameters['contact'];
		if(isset($contact->CATEGORIES)) {
			\OCP\Util::writeLog('contacts', __METHOD__.' groups: '.print_r($contact->CATEGORIES->getParts(), true), \OCP\Util::DEBUG);
			$tagMgr = \OC::$server->getTagManager()->load('contact');
			foreach($contact->CATEGORIES->getParts() as $group) {
				\OCP\Util::writeLog('contacts', __METHOD__.' group: '.$group, \OCP\Util::DEBUG);
				$tagMgr->tagAs($parameters['id'], $group);
			}
		}
		Utils\Properties::updateIndex($parameters['id'], $contact);
	}

	public static function contactUpdated($parameters) {
		//\OCP\Util::writeLog('contacts', __METHOD__.' parameters: '.print_r($parameters, true), \OCP\Util::DEBUG);
		$contact = $parameters['contact'];
		Utils\Properties::updateIndex($parameters['contactId'], $contact);
		// If updated via CardDAV we don't know if PHOTO has changed
		if(isset($parameters['carddav']) && $parameters['carddav']) {
			if(isset($contact->PHOTO) || isset($contact->LOGO)) {
				Utils\Properties::cacheThumbnail(
					$parameters['backend'],
					$parameters['addressBookId'],
					$parameters['contactId'],
					null,
					$contact,
					array('update' => true)
				);
			}
			$tagMgr = \OC::$server->getTagManager()->load('contact');
			$tagMgr->purgeObjects(array($parameters['contactId']));
			if(isset($contact->CATEGORIES)) {
				$tagMgr->addMultiple($contact->CATEGORIES->getParts(), true, $parameters['contactId']);
			}
		}
	}

	/**
	 * Scan vCards for categories.
	 */
	public static function scanCategories() {
		$offset = 0;
		$limit = 10;

		$tagMgr = \OC::$server->getTagManager()->load('contact');
		$tags = array();

		foreach ($tagMgr->getTags() as $tag) {
			$tags[] = $tag['name'];
		}

		// reset tags
		$tagMgr->delete($tags);

		$app = new App();
		$backend = $app->getBackend('local');
		$addressBookInfos = $backend->getAddressBooksForUser();

		foreach ($addressBookInfos as $addressBookInfo) {
			$addressBook = new AddressBook($backend, $addressBookInfo);
			while ($contacts = $addressBook->getChildren($limit, $offset, false)) {
				foreach ($contacts as $contact) {
					if (isset($contact->CATEGORIES)) {
						$tagMgr->addMultiple($contact->CATEGORIES->getParts(), true, $contact->getId());
					}
				}
				\OCP\Util::writeLog('contacts',
					__METHOD__ .', scanning: ' . $limit . ' starting from ' . $offset,
					\OCP\Util::DEBUG);
				$offset += $limit;
			}
		}
	}

	/**
	 * Scan vCards for properties.
	 */
	public static function indexProperties() {
		$offset = 0;
		$limit = 10;

		$app = new App();
		$backend = $app->getBackend('local');
		$addressBookInfos = $backend->getAddressBooksForUser();

		foreach ($addressBookInfos as $addressBookInfo) {
			$addressBook = new AddressBook($backend, $addressBookInfo);
			$contacts = $addressBook->getChildren($limit, $offset, false);
			\OCP\Util::writeLog('contacts',
				__METHOD__ . ', indexing: ' . $limit . ' starting from ' . $offset,
				\OCP\Util::DEBUG);
			foreach ($contacts as $contact) {
				if(!$contact->retrieve()) {
					\OCP\Util::writeLog('contacts',
						__METHOD__ . ', Error loading contact ' .print_r($contact, true),
						\OCP\Util::DEBUG);
				}
				Utils\Properties::updateIndex($contact->getId(), $contact);
			}
			$offset += $limit;
		}
		$stmt = \OCP\DB::prepare('DELETE FROM `*PREFIX*contacts_cards_properties`
							WHERE NOT EXISTS(SELECT NULL
							FROM `*PREFIX*contacts_cards`
							WHERE `*PREFIX*contacts_cards`.id = `*PREFIX*contacts_cards_properties`.contactid)');
		$result = $stmt->execute(array());
	}

	public static function getCalenderSources($parameters) {
		//\OCP\Util::writeLog('contacts', __METHOD__.' parameters: '.print_r($parameters, true), \OCP\Util::DEBUG);

		$app = new App();
		$addressBooks = $app->getAddressBooksForUser();
		$baseUrl = \OCP\Util::linkTo('calendar', 'ajax/events.php').'?calendar_id=';

		foreach ($addressBooks as $addressBook) {
			$info = $addressBook->getMetaData();
			$parameters['sources'][]
				= array(
					'url' => $baseUrl . 'birthday_'. $info['backend'].'_' . $info['id'],
					'backgroundColor' => '#cccccc',
					'borderColor' => '#888',
					'textColor' => 'black',
					'cache' => true,
					'editable' => false,
				);
		}
	}

	public static function getBirthdayEvents($parameters) {
		//\OCP\Util::writeLog('contacts', __METHOD__.' parameters: '.print_r($parameters, true), \OCP\Util::DEBUG);
		$name = $parameters['calendar_id'];

		if (strpos($name, 'birthday_') != 0) {
			return;
		}

		$info = explode('_', $name);
		$backend = $info[1];
		$aid = $info[2];
		$app = new App();
		$addressBook = $app->getAddressBook($backend, $aid);

		foreach ($addressBook->getBirthdayEvents() as $vevent) {
			$parameters['events'][] = array(
				'id' => 0,
				'vevent' => $vevent,
				'repeating' => true,
				'summary' => $vevent->SUMMARY,
				'calendardata' => $vevent->serialize()
			);
		}
	}
}
