<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts;

use Sabre\VObject,
	OCP\AppFramework,
	OCA\Contacts\Controller\AddressBookController,
	OCA\Contacts\Controller\BackendController,
	OCA\Contacts\Controller\GroupController,
	OCA\Contacts\Controller\ContactController,
	OCA\Contacts\Controller\ContactPhotoController,
	OCA\Contacts\Controller\SettingsController,
	OCA\Contacts\Controller\ImportController;

/**
 * This class manages our app actions
 *
 * TODO: Merge in Dispatcher
 */
App::$l10n = \OC_L10N::get('contacts');

class App {

	/**
	* @brief Categories of the user
	* @var OC_VCategories
	*/
	public static $categories = null;

	/**
	 * @brief language object for calendar app
	 *
	 * @var OC_L10N
	 */
	public static $l10n;

	/**
	 * An array holding the current users address books.
	 * @var array
	 */
	protected static $addressBooks = array();
	/**
	* If backends are added to this map, they will be automatically mapped
	* to their respective classes, if constructed with the 'getBackend' method.
	*
	* @var array
	*/
	public static $backendClasses = array(
		'local' => 'OCA\Contacts\Backend\Database',
		'shared' => 'OCA\Contacts\Backend\Shared',
//		'localusers' => 'OC\Contacts\Backend\LocalUsers',
	);

	public function __construct(
		$user = null,
		$addressBooksTableName = '*PREFIX*addressbook',
		$backendsTableName = '*PREFIX*addressbooks_backend',
		$dbBackend = null
	) {
		$this->user = $user ? $user : \OCP\User::getUser();
		$this->addressBooksTableName = $addressBooksTableName;
		$this->backendsTableName = $backendsTableName;
		$this->dbBackend = $dbBackend
			? $dbBackend
			: new Backend\Database($user);
		if (\OCP\Config::getAppValue('contacts', 'backend_ldap', "false") === "true") {
			self::$backendClasses['ldap'] = 'OCA\Contacts\Backend\Ldap';
		}
	}

	/**
	* Gets backend by name.
	*
	* @param string $name
	* @return \Backend\AbstractBackend
	*/
	public function getBackend($name) {
		$name = $name ? $name : 'local';
		if (isset(self::$backendClasses[$name])) {
			return new self::$backendClasses[$name]($this->user);
		} else {
			throw new \Exception('No backend for: ' . $name, '404');
		}
	}

	/**
	 * Return all registered address books for current user.
	 * For now this is hard-coded to using the Database and
	 * Shared backends, but eventually admins will be able to
	 * register additional backends, and users will be able to
	 * subscribe to address books using those backends.
	 *
	 * @return AddressBook[]
	 */
	public function getAddressBooksForUser() {
		if (!self::$addressBooks) {
			foreach (array_keys(self::$backendClasses) as $backendName) {
				$backend = self::getBackend($backendName, $this->user);
				$addressBooks = $backend->getAddressBooksForUser();
				if ($backendName === 'local' && count($addressBooks) === 0) {
					$id = $backend->createAddressBook(array('displayname' => self::$l10n->t('Contacts')));
					if ($id !== false) {
						$addressBook = $backend->getAddressBook($id);
						$addressBooks = array($addressBook);
					} else {
						\OCP\Util::writeLog(
							'contacts',
							__METHOD__ . ', Error creating default address book',
							\OCP\Util::ERROR
						);
					}

				}

				foreach ($addressBooks as $addressBook) {
					$addressBook['backend'] = $backendName;
					self::$addressBooks[] = new AddressBook($backend, $addressBook);
				}

			}

		}

		return self::$addressBooks;
	}

	/**
	 * Get an address book from a specific backend.
	 *
	 * @param string $backendName
	 * @param string $addressbookid
	 * @return AddressBook|null
	 */
	public function getAddressBook($backendName, $addressbookid) {
		//\OCP\Util::writeLog('contacts', __METHOD__ . ': '. $backendName . ', ' . $addressbookid, \OCP\Util::DEBUG);
		foreach (self::$addressBooks as $addressBook) {
			if ($addressBook->getBackend()->name === $backendName
				&& $addressBook->getId() === $addressbookid
			) {
				return $addressBook;
			}
		}

		$backend = self::getBackend($backendName, $this->user);
		$info = $backend->getAddressBook($addressbookid);

		if (!$info) {
			throw new \Exception(self::$l10n->t('Address book not found'), 404);
		}
		
		$addressBook = new AddressBook($backend, $info);
		self::$addressBooks[] = $addressBook;
		return $addressBook;
	}

	/**
	 * Get a Contact from an address book from a specific backend.
	 *
	 * @param string $backendName
	 * @param string $addressbookid
	 * @param string $id - Contact id
	 * @return Contact|null
	 *
	 */
	public function getContact($backendName, $addressbookid, $id) {
		$addressBook = $this->getAddressBook($backendName, $addressbookid);
		return $addressBook->getChild($id);
	}

}
