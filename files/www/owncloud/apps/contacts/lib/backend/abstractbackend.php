<?php
/**
 * ownCloud - Base class for Contacts backends
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

use OCA\Contacts\VObject\VCard;

/**
 * Subclass this class for address book backends.
 */
abstract class AbstractBackend {

	/**
	* The following methods MUST be implemented:
	*
	* @method array getAddressBooksForUser(array $options = array())
	* @method array|null getAddressBook(string $addressbookid, array $options = array())
	* @method array getContacts(string $addressbookid, array $options = array())
	* @method array|null getContact(string $addressbookid, mixed $id, array $options = array())
	* The following methods MAY be implemented:
	* @method bool hasAddressBook(string $addressbookid)
	* @method bool updateAddressBook(string $addressbookid, array $updates, array $options = array())
	* @method string createAddressBook(array $properties, array $options = array())
	* @method bool deleteAddressBook(string $addressbookid, array $options = array())
	* @method int lastModifiedAddressBook(string $addressbookid)
	* @method array numContacts(string $addressbookid)
	* @method bool updateContact(string $addressbookid, string $id, VCard $contact, array $options = array())
	* @method string createContact(string $addressbookid, VCard $contact, array $properties)
	* @method bool deleteContact(string $addressbookid, string $id, array $options = array())
	* @method int lastModifiedContact(string $addressbookid)
	*/

	/**
	 * The name of the backend.
	 * @var string
	 */
	public $name;

	/**
	 * The current usert.
	 * @var string
	 */
	public $userid;

	protected $possibleContactPermissions = array(
		\OCP\PERMISSION_CREATE 	=> 'createContact',
		\OCP\PERMISSION_READ	=> 'getContact',
		\OCP\PERMISSION_UPDATE	=> 'updateContact',
		\OCP\PERMISSION_DELETE 	=> 'deleteContact',
	);

	protected $possibleAddressBookPermissions = array(
		\OCP\PERMISSION_CREATE 	=> 'createAddressBook',
		\OCP\PERMISSION_READ	=> 'getAddressBook',
		\OCP\PERMISSION_UPDATE	=> 'updateAddressBook',
		\OCP\PERMISSION_DELETE 	=> 'deleteAddressBook',
	);

	/**
	* Sets up the backend
	*
	*/
	public function __construct($userid = null) {
		$this->userid = $userid ? $userid : \OCP\User::getUser();
	}

	/**
	* @brief Get all possible permissions for contacts based on what the backend implements.
	* @returns bitwise-or'ed actions
	*
	* Returns the supported actions as an int to be
	* compared with \OCP\PERMISSION_CREATE etc.
	*/
	protected function getContactPermissions() {
		$permissions = 0;

		foreach ($this->possibleContactPermissions as $permission => $methodName) {
			if(method_exists($this, $methodName)) {
				$permissions |= $permission;
			}

		}

		//\OCP\Util::writeLog('contacts', __METHOD__.', permissions' . $permissions, \OCP\Util::DEBUG);
		return $permissions;
	}

	/**
	* @brief Get all permissions for address book based on what the backend implements.
	* @returns bitwise-or'ed actions
	*
	* Returns the supported actions as int to be
	* compared with \OCP\PERMISSION_CREATE etc.
	*/
	protected function getAddressBookPermissions() {

		$permissions = 0;

		foreach ($this->possibleAddressBookPermissions as $permission => $methodName) {
			if (method_exists($this, $methodName)) {
				$permissions |= $permission;
			}

		}

		//\OCP\Util::writeLog('contacts', __METHOD__.', permissions' . $permissions, \OCP\Util::DEBUG);
		return $permissions;
	}

	/**
	* @brief Check if backend implements action for contacts
	* @param $actions bitwise-or'ed actions
	* @returns boolean
	*
	* Returns the supported actions as int to be
	* compared with \OCP\PERMISSION_CREATE etc.
	*/
	public function hasContactMethodFor($permission) {

		return (bool)($this->getContactPermissions() & $permission);

	}

	/**
	* @brief Check if backend implements action for contacts
	* @param $actions bitwise-or'ed actions
	* @returns boolean
	*
	* Returns the supported actions as int to be
	* compared with \OCP\PERMISSION_CREATE etc.
	*/
	public function hasAddressBookMethodFor($permission) {

		return (bool)($this->getAddressBookPermissions() & $permission);

	}

	/**
	 * Check if the backend has the address book
	 *
	 * This can be reimplemented in the backend to improve performance.
	 *
	 * @param string $addressBookId
	 * @return bool
	 */
	public function hasAddressBook($addressBookId) {

		return count($this->getAddressBook($addressBookId)) > 0;

	}

	/**
	 * Returns the number of contacts in an address book.
	 * Implementations can choose to override this method to either
	 * get the result more effectively or to return null if the backend
	 * cannot determine the number.
	 *
	 * @param string $addressBookId
	 * @return integer|null
	 */
	public function numContacts($addressBookId) {

		return count($this->getContacts($addressBookId));

	}

	/**
	 * Returns the list of addressbooks for a specific user.
	 *
	 * The returned arrays MUST contain a unique 'id' for the
	 * backend and a 'displayname', and it MAY contain a
	 * 'description'.
	 *
	 * @param array $options - Optional (backend specific options)
	 * @return array
	 */
	public abstract function getAddressBooksForUser(array $options = array());

	/**
	 * Get an addressbook's properties
	 *
	 * The returned array MUST contain string: 'displayname',string: 'backend'
	 * and integer: 'permissions' value using there ownCloud CRUDS constants
	 * (which MUST be at least \OCP\PERMISSION_READ).
	 * Currently the only ones supported are 'displayname' and
	 * 'description', but backends can implement additional.
	 *
	 * @param string $addressBookId
	 * @param array $options - Optional (backend specific options)
	 * @return array|null $properties
	 */
	public abstract function getAddressBook($addressBookId, array $options = array());

	/**
	 * Updates an addressbook's properties
	 *
	 * The $properties array contains the changes to be made.
	 *
	 * Currently the only ones supported are 'displayname' and
	 * 'description', but backends can implement additional.
	 *
	 * @param string $addressBookId
	 * @param array $properties
	 * @param array $options - Optional (backend specific options)
	 * @return bool
	public function updateAddressBook($addressBookId, array $properties, array $options = array());
	 */

	/**
	 * Creates a new address book
	 *
	 * Classes that doesn't support adding address books MUST NOT implement this method.
	 *
	 * Currently the only ones supported are 'displayname' and
	 * 'description', but backends can implement additional.
	 * 'displayname' MUST be present.
	 *
	 * @param array $properties
	 * @param array $options - Optional (backend specific options)
	 * @return string|false The ID if the newly created AddressBook or false on error.
	public function createAddressBook(array $properties, array $options = array());
	 */

	/**
	 * Deletes an entire address book and all its contents
	 *
	 * Classes that doesn't support deleting address books MUST NOT implement this method.
	 *
	 * @param string $addressBookId
	 * @param array $options - Optional (backend specific options)
	 * @return bool
	public function deleteAddressBook($addressBookId, array $options = array());
	 */

	/**
	 * @brief Get the last modification time for an address book.
	 *
	 * Must return a UNIX time stamp or null if the backend
	 * doesn't support it.
	 *
	 * @param string $addressBookId
	 * @returns int | null
	 */
	public function lastModifiedAddressBook($addressBookId) {
	}

	/**
	 * @brief 'touch' an address book.
	 *
	 * If implemented this method must mark the address books
	 * modification date so lastModifiedAddressBook() can be
	 * used to invalidate the cache.
	 *
	 * @param string $addressBookId
	 */
	public function setModifiedAddressBook($addressBookId) {
	}

	/**
	 * Returns all contacts for a specific addressbook id.
	 *
	 * The returned array MUST contain the unique ID a string value 'id', a string
	 * value 'displayname', a string value 'owner' and an integer 'permissions' value using there
	 * ownCloud CRUDS constants (which MUST be at least \OCP\PERMISSION_READ), and SHOULD
	 * contain the properties of the contact formatted as a vCard 3.0
	 * https://tools.ietf.org/html/rfc2426 mapped to 'carddata' or as an
	 * \OCA\Contacts\VObject\VCard object mapped to 'vcard'.
	 *
	 * Example:
	 *
	 * array(
	 *   0 => array('id' => '4e111fef5df', 'owner' => 'foo', 'permissions' => 1, 'displayname' => 'John Q. Public', 'vcard' => $vobject),
	 *   1 => array('id' => 'bbcca2d1535', 'owner' => 'bar', 'permissions' => 32, 'displayname' => 'Jane Doe', 'carddata' => $data)
	 * );
	 *
	 * For contacts that contain loads of data, the 'carddata' or 'vcard' MAY be omitted
	 * as it can be fetched later.
	 *
	 * The following options are supported in the $options array:
	 *
	 * - 'limit': An integer value for the number of contacts to fetch in each call.
	 * - 'offset': The offset to start at.
	 * - 'omitdata': Whether to fetch the entire carddata or vcard.
	 *
	 * @param string $addressBookId
	 * @param array $options - Optional options
	 * @return array
	 */
	public abstract function getContacts($addressBookId, array $options = array());

	/**
	 * Returns a specfic contact.
	 *
	 * Same as getContacts except that either 'carddata' or 'vcard' is mandatory.
	 *
	 * @param string $addressBookId
	 * @param mixed $id
	 * @param array $options - Optional options
	 * @return array|null
	 */
	public abstract function getContact($addressBookId, $id, array $options = array());

	/**
	 * Creates a new contact
	 *
	 * Classes that doesn't support adding contacts MUST NOT implement this method.
	 *
	 * @param string $addressBookId
	 * @param VCard $contact
	 * @param array $options - Optional options
	 * @return string|bool The identifier for the new contact or false on error.
	public function createContact($addressbookid, $contact, array $options = array());
	 */

	/**
	 * Updates a contact
	 *
	 * Classes that doesn't support updating contacts MUST NOT implement this method.
	 *
	 * @param string $addressBookId
	 * @param mixed $id
	 * @param VCard $contact
	 * @param array $options - Optional options
	 * @return bool
	public function updateContact($addressbookid, $id, $carddata, array $options = array());
	 */

	/**
	 * Deletes a contact
	 *
	 * Classes that doesn't support deleting contacts MUST NOT implement this method.
	 *
	 * @param string $addressBookId
	 * @param mixed $id
	 * @param array $options - Optional options
	 * @return bool
	public function deleteContact($addressbookid, $id, array $options = array());
	 */

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
	}
	
	/**
	 * Creates a unique key for inserting into oc_preferences.
	 * As IDs can have any length and the key field is limited to 64 chars,
	 * the IDs are transformed to the first 8 chars of their md5 hash.
	 * 
	 * @param string $addressBookId.
	 * @param string $contactId.
	 * @throws \BadMethodCallException
	 * @return string
	 */
	protected function combinedKey($addressBookId = null, $contactId = null) {
		$key = $this->name;
		if (!is_null($addressBookId)) {

			$key .= '_' . substr(md5($addressBookId), 0, 8);

			if (!is_null($contactId)) {
				$key .= '_' . substr(md5($contactId), 0, 8);
			}

		} else if (!is_null($contactId)) {

			throw new \BadMethodCallException(
				__METHOD__ . ' cannot be called with a contact ID but no address book ID'
			);

		}
		return $key;
	}

	/**
	 * @brief Query whether a backend or an address book is active
	 * @param string $addressbookid If null it checks whether the backend is activated.
	 * @return boolean
	 */
	public function isActive($addressBookId = null) {

		$key = $this->combinedKey($addressBookId);
		$key = 'active_' . $key;

		return !!(\OCP\Config::getUserValue($this->userid, 'contacts', $key, 1));
	}

	/**
	 * @brief Activate a backend or an address book
	 * @param bool active
	 * @param string $addressbookid If null it activates the backend.
	 * @return boolean
	 */
	public function setActive($active, $addressBookId = null) {

		$key = $this->combinedKey($addressBookId);
		$key = 'active_' . $key;

		$this->setModifiedAddressBook($addressBookId);
		return \OCP\Config::setUserValue($this->userid, 'contacts', $key, (int)$active);
	}

	/**
	 * @brief get all the preferences for the addressbook
	 * @param string $id
	 * @return array Format array('param1' => 'value', 'param2' => 'value')
	 */
	public function getPreferences($addressBookId) {

		$key = 'prefs_' . $this->combinedKey($addressBookId);

		$data = \OCP\Config::getUserValue($this->userid, 'contacts', $key, false);
		return $data ? json_decode($data, true) : array();
	}
	
	/**
	 * @brief sets the preferences for the addressbook given in parameter
	 * @param string $id
	 * @param array the preferences, format array('param1' => 'value', 'param2' => 'value')
	 * @return boolean
	 */
	public function setPreferences($addressbookid, array $params) {
		$key = 'prefs_' . $this->combinedKey($addressbookid);

		$data = json_encode($params);
		return $data
			? \OCP\Config::setUserValue($this->userid, 'contacts', $key, $data)
			: false;
	}
	
	public function removePreferences($addressbookid) {
		$key = $this->combinedKey($addressbookid);
		$key = 'prefs_' . $key;
		
		\OC_Preferences::deleteKey( $this->userid, 'contacts', $key );
		
	}
}
