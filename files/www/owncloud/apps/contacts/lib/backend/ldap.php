<?php
/**
 * ownCloud - Base class for Contacts backends
 *
 * @author Nicolas Mora
 * @copyright 2013 Nicolas Mora (mail@babelouest.org)
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.	If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Contacts\Backend;

use OCA\Contacts\Contact;
use OCA\Contacts\VObject\VCard;
use Sabre\VObject\Reader;
use OCA\Contacts\Connector\LdapConnector;

/**
 * Subclass this class for Cantacts backends
 */

class Ldap extends AbstractBackend {

	/**
	 * The name of the backend.
	 * @var string
	 */
	public $name='ldap';
	private $ldapConnection = null;
	private $connector = null;
	
	/**
	 * The cached address books.
	 * @var array[]
	 */
	public $addressbooks;

	/**
	* @brief validates and sets the ldap parameters
	* @param $ldapParams array containing the parameters
	* return boolean
	*/
	public function setLdapParams($aid) {
		$tmp = $this->getPreferences($aid);
		if ($tmp != false) {
			$this->ldapParams = (array)$tmp;
			$this->connector = new LdapConnector($this->ldapParams['ldap_vcard_connector']);
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @brief creates the ldap connection, then binds it according to the parameters previously given
	 * @return boolean connexion status
	 */
	public function ldapCreateAndBindConnection() {
		if (!self::ldapIsConnected() && $this->ldapParams != null) {
			// ldap connect
			$this->ldapConnection = ldap_connect($this->ldapParams['ldapurl']);
			ldap_set_option($this->ldapConnection, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
			if ($this->ldapConnection) {
				// ldap bind
				if ($this->ldapParams['ldapanonymous'] == 1) {
					$ldapbind = ldap_bind($this->ldapConnection);
				} else {
					$ldapbind = ldap_bind($this->ldapConnection, $this->ldapParams['ldapuser'], base64_decode($this->ldapParams['ldappass']));
				}
				return $ldapbind;
			}
			return false;
		}
		return self::ldapIsConnected();
	}
	
	/**
	 * @brief close the current connection
	 * @return boolean closing success
	 */
	public function ldapCloseConnection() {
		if (self::ldapIsConnected()) {
			ldap_unbind($this->ldapConnection);
			$this->ldapConnection = null;
			return true;
		}
	}
	
	/**
	 * 
	 */
	public function ldapIsConnected() {
		return ($this->ldapConnection != null);
	}
	
	/**
	 * @brief search a list in ldap server
	 * @param $ldapbasedn the base dn
	 * @param $bindsearch the search filter
	 * @param $entries the ldap entries to reach
	 * @param $start the starting point
	 * @param $num the number of entries to return
	 * @return array|false
	 */
	public function ldapFindMultiple($ldapbasedn, $bindsearch, $entriesName, $start=null, $num=null) {
		if (($entriesName != null) && self::ldapCreateAndBindConnection() && $ldapbasedn != null && $bindsearch != null) {
			
			if ($start==null) {
				$start=0;
			}
			
			if ($num==null) {
				$num=PHP_INT_MAX;
			}
			
			$pageSize = isset($this->ldapParams['ldappagesize'])?$this->ldapParams['ldappagesize']:"20";

			$cookie = '';
			
			$entries = array();
			
			$cpt=0;
			
			\OC_Log::write('contacts_ldap', __METHOD__." - search what $ldapbasedn, $bindsearch $pageSize", \OC_Log::DEBUG);
			do {
				
				ldap_control_paged_result($this->ldapConnection, $pageSize, true, $cookie);
				$ldap_results = ldap_search ($this->ldapConnection, $ldapbasedn, $bindsearch, $entriesName);
				
				$LdapEntries = ldap_get_entries ($this->ldapConnection, $ldap_results);
				
				for ($i=0; $i<$LdapEntries['count']; $i++) {
					$entries[] = $LdapEntries[$i];
				}
				ldap_control_paged_result_response($this->ldapConnection, $ldap_results, $cookie);

				$cpt++;
			} while($cookie !== null && $cookie != '' && $cpt < 10);
			
			$entries['count'] = count($entries);
			
			return $entries;

			self::ldapCloseConnection();
		}
		return false;
	}
	
	
	/**
	* @brief search one contact in ldap server
	* @param $ldapbasedn the base dn
	* @param $bindsearch the search filter
	* @param $entries the ldap entries to reach
	* @param $start the starting point
	* @param $num the number of entries to return
	* @return array|false
	*/
	public function ldapFindOne($ldapbasedn, $bindsearch, $entries, $start=null, $num=null) {
		if (($entries != null) && self::ldapCreateAndBindConnection() && $ldapbasedn != null && $bindsearch != null) {

			if ($start==null) {
				$start=0;
			}

			if ($num==null) {
				$num=PHP_INT_MAX;
			}
			\OC_Log::write('contacts_ldap', __METHOD__." - search $ldapbasedn, $bindsearch ", \OC_Log::DEBUG);

			$ldap_results = @ldap_search ($this->ldapConnection, $ldapbasedn, $bindsearch, $entries);
			if ($ldap_results) {
				$entries = ldap_get_entries ($this->ldapConnection, $ldap_results);
				if ($entries['count'] > 0) {
					return $entries[0];
				} else {
					return false;
				}
			}

			self::ldapCloseConnection();
		}
		return false;
	}

	/**
	 * @brief adds a new ldap entry
	 * @param $ldapDN the new DN (must be unique)
	 * @param $ldapValues the ldif values
	 * @return boolean insert status
	 */
	public function ldapAdd($ldapDN, $ldapValues) {
		if (self::ldapIsConnected()) {
			return @ldap_add($this->ldapConnection, $ldapDN, $ldapValues);
		}
		return false;
	}
	
	/**
	 * @brief modify a ldap entry
	 * @param $ldapDN the DN (must exists)
	 * @param $ldapValues the ldif values
	 * @return boolean modify status
	 */
	public function ldapUpdate($ldapDN, $ldapValues) {
		if (self::ldapIsConnected()) {
			return @ldap_modify($this->ldapConnection, $ldapDN, $ldapValues);
		}
		return false;
	}
	
	/**
	 * @brief delete a ldap entry
	 * @param $ldapDN the DN (must exists)
	 * @return boolean delete status
	 */
	public function ldapDelete($ldapDN) {
		if (self::ldapIsConnected()) {
			ldap_delete($this->ldapConnection, $ldapDN);
			return true;
		}
		return false;
	}
	
	/**
	* Sets up the backend
	*
	* @param string $cardsTableName
	*/
	public function __construct(
		$userid = null,
		$addressBooksTableName = '*PREFIX*contacts_ldap_addressbooks'
	) {
		$this->userid = $userid ? $userid : \OCP\User::getUser();
		$this->addressbooks = array();
	}

	/**
	 * Returns the list of active addressbooks for a specific user.
 	 *
	 * @param string $userid
	 * @return array
	 */
	public function getAddressBooksForUser(array $options = array()) {
		$addressbookidList = $this->getAddressbookList();
		foreach($addressbookidList as $addressbookid) {
			$this->addressbooks[] = self::getAddressBook($addressbookid);
		}
		return $this->addressbooks;
	}

	/**
	 * Get an addressbook's properties
	 *
	 * The returned array MUST contain 'displayname' and an integer 'permissions'
	 * value using there ownCloud CRUDS constants (which MUST be at least
	 * \OCP\PERMISSION_READ).
	 * Currently the only ones supported are 'displayname' and
	 * 'description', but backends can implement additional.
	 *
	 * @param string $addressbookid
	 * @return array $properties
	 */
	public function getAddressBook($addressbookid, array $options = array()) {
		//\OC_Log::write('contacts', __METHOD__.' id: '
		//	. $addressbookid, \OC_Log::DEBUG);
		if($this->addressbooks && isset($this->addressbooks[$addressbookid])) {
			//print(__METHOD__ . ' ' . __LINE__ .' addressBookInfo: ' . print_r($this->addressbooks[$addressbookid], true));
			return $this->addressbooks[$addressbookid];
		}
		// Hmm, not found. Lets query the db.
		$preferences = self::getPreferences($addressbookid);
		if (count($preferences) > 0) {
			$preferences['id'] = (string)$addressbookid;
			$preferences['backend'] = $this->name;
			$preferences['owner'] = $this->userid;
			$preferences['permissions'] = \OCP\PERMISSION_ALL;
			$preferences['lastmodified'] = self::lastModifiedAddressBook($addressbookid);
			
			// remove the ldappassword from the return value if exists
			if (isset($preferences['ldappass']) && (isset($options['getpwd']) && !$options['getpwd'])) {
				unset($preferences['ldappass']);
			}
			
			return $preferences;
		} else {
			return array();
		}
	}

	/**
	 * Test if the address book exists
	 * @return bool
	 */
	public function hasAddressBook($addressbookid) {
		if($this->addressbooks && isset($this->addressbooks[$addressbookid])) {
			return true;
		}
		return count($this->getAddressBook($addressbookid)) > 0;
	}

	/**
	 * Updates an addressbook's properties
	 *
	 * The $properties array contains the changes to be made.
	 *
	 * Currently the only ones supported are 'displayname' and
	 * 'description', but backends can implement additional.
	 *
	 * @param string $addressbookid
	 * @param array $properties
	 * @return string|false The ID if the modified AddressBook or false on error.
	 */
	public function updateAddressBook($addressbookid, array $properties, array $options = array()) {
		if ($this->hasAddressBook($addressbookid)) {
			// Addressbook exists, modify it through the create function
			if (isset($properties['ldappassmodified']) && $properties['ldappassmodified'] != 'true') {
				// If password hasn't changed, get it from the preferences
				$addrbook = $this->getAddressBook($addressbookid, array('getpwd' => true));
				$properties['ldappass'] = base64_decode($addrbook['ldappass']);
			}
			return $this->setAddressBook($properties, $options);
		} else {
			return false;
		}
	}
	
	/**
	 * Creates a new address book
	 *
	 * Currently the only ones supported are 'displayname' and
	 * 'description', but backends can implement additional.
	 * 'displayname' MUST be present.
	 *
	 * @param array $properties
	 * @return string|false The ID if the newly created AddressBook or false on error.
	 */
	public function createAddressBook(array $properties, array $options = array()) {
		if (!isset($properties['uri']) || $this->hasAddressBook($properties['uri'])) {
			return false;
		} else {
			return $this->setAddressBook($properties, $options);
		}
	}

	/*
	 * Sets the parameters for a new or existing addressbook
	 *
	 * @param array $properties
	 * @return string|false The ID if the newly created AddressBook or false on error.
	 */
	public function setAddressBook(array $properties, array $options = array()) {
		if (count($properties) === 0) {
			return false;
		}
		if (isset($properties['displayname']) && $properties['displayname'] != '' &&
			isset($properties['uri']) && $properties['uri'] != '' &&
			isset($properties['ldapurl']) && $properties['ldapurl'] != '' &&
			isset($properties['ldappagesize']) && $properties['ldappagesize'] != '' &&
			isset($properties['ldapbasednsearch']) && $properties['ldapbasednsearch'] != '' &&
			isset($properties['ldapfilter']) && $properties['ldapfilter'] != '' &&
			isset($properties['ldapvcardconnector']) &&
			isset($properties['ldapanonymous']) &&
				($properties['ldapanonymous']=='true' 
					|| ($properties['ldapanonymous']=='false' 
						&& isset($properties['ldapuser']) && $properties['ldapuser'] != ''
						&& isset($properties['ldappass']) && $properties['ldappass'] != ''
						)
				) &&
			isset($properties['ldapreadonly']) &&
				($properties['ldapreadonly']=='true' 
					|| ($properties['ldapreadonly']=='false' 
						&& isset($properties['ldapbasednmodify']) && $properties['ldapbasednmodify'] != ''
						)
				)
			) {
			$addressbookSettings = array();
			$addressbookSettings['uri'] = $properties['uri'];
			$addressbookSettings['displayname'] = $properties['displayname'];
			$addressbookSettings['description'] = $properties['description'];
			$addressbookSettings['ldapurl'] = $properties['ldapurl'];
			$addressbookSettings['ldapanonymous'] = ($properties['ldapanonymous']=='true');
			$addressbookSettings['ldapreadonly'] = ($properties['ldapreadonly']=='true');
			$addressbookSettings['ldapuser'] = ($properties['ldapanonymous']=='false')?$properties['ldapuser']:"";
			$addressbookSettings['ldappass'] = ($properties['ldapanonymous']=='false')?base64_encode($properties['ldappass']):"";
			$addressbookSettings['ldappagesize'] = $properties['ldappagesize'];
			$addressbookSettings['ldapbasednsearch'] = $properties['ldapbasednsearch'];
			$addressbookSettings['ldapfilter'] = $properties['ldapfilter'];
			$addressbookSettings['ldapbasednmodify'] = ($properties['ldapanonymous']=='false')?$properties['ldapbasednmodify']:"";
			$addressbookSettings['ldapconnectorid'] = $properties['ldapvcardconnector'];
			
			if ($properties['ldapvcardconnector'] != '') {
				$prefix = "backend_ldap_";
				$suffix = "_connector.xml";
				$path = __DIR__ . "/../../formats/";
				if (file_exists( $path.$prefix.$properties['ldapvcardconnector'].$suffix )) {
					$addressbookSettings['ldap_vcard_connector'] = file_get_contents ( $path.$prefix.$properties['ldapvcardconnector'].$suffix );
				}
			} else {
				$addressbookSettings['ldap_vcard_connector'] = $properties['ldapvcardconnectorvalue'];
			}

			$addressbookList = $this->getAddressbookList();
			if (!in_array($properties['uri'], $addressbookList)) {
				$addressbookList[] = $properties['uri'];
				$this->setAddressbookList($addressbookList);
			}
			$this->setPreferences($properties['uri'], $addressbookSettings);
			$this->setActive(1, $properties['uri']);
			return $properties['uri'];
		}
		return false;
	}

	/**
	 * Deletes an entire addressbook and all its contents
	 *
	 * @param string $addressbookid
	 * @return bool
	 */
	public function deleteAddressBook($addressbookid, array $options = array()) {
		//$addressbook = self::getAddressBook($addressbookid);
		$addressbookList = $this->getAddressbookList();
		$toRemove = array_search($addressbookid, $addressbookList);
		if (is_int($toRemove)) {
			unset($addressbookList[$toRemove]);
			$addressbookList = array_values($addressbookList);
			$this->setAddressbookList($addressbookList);
		}

		self::removePreferences($addressbookid);

		// TODO: use backend settings
		return true;
	}

	/**
	 * Returns all contacts for a specific addressbook id.
	 *
	 * The returned array MUST contain the unique ID of the contact mapped to 'id', a
	 * displayname mapped to 'displayname' and an integer 'permissions' value using there
	 * ownCloud CRUDS constants (which MUST be at least \OCP\PERMISSION_READ), and SHOULD
	 * contain the properties of the contact formatted as a vCard 3.0
	 * https://tools.ietf.org/html/rfc2426 mapped to 'carddata' or as an
	 * \OCA\Contacts\VObject\VCard object mapped to 'vcard'.
	 *
	 * Example:
	 *
	 * array(
	 *	 0 => array('id' => '4e111fef5df', 'permissions' => 1, 'displayname' => 'John Q. Public', 'vcard' => $object),
	 *	 1 => array('id' => 'bbcca2d1535', 'permissions' => 32, 'displayname' => 'Jane Doe', 'carddata' => $data)
	 * );
	 *
	 * For contacts that contain loads of data, the 'carddata' or 'vcard' MAY be omitted
	 * as it can be fetched later.
	 *
	 * TODO: Some sort of ETag?
	 *
	 * @param string $addressbookid
	 * @param bool $omitdata Don't fetch the entire carddata or vcard.
	 * @return array
	 */
	public function getContacts($addressbookid, array $options = array()) {
		$backtrace = debug_backtrace();
		$trace=array();
		foreach ($backtrace as $elt) {
			foreach ($elt as $key => $line) {
				if ($key == "file" || $key == "line") {
					$trace[] = $line;
				}
			}
		}
		
		$cards = array();
		$vcards = array();
		if(is_array($addressbookid) && count($addressbookid)) {
			$id_array = $addressbookid;
		} elseif(is_int($addressbookid) || is_string($addressbookid)) {
			$id_array = array($addressbookid);
		} else {
			\OC_Log::write('contacts_ldap', __METHOD__.'. Addressbook id(s) argument is empty: '. print_r($id, true), \OC_Log::DEBUG);
			return false;
		}
		
		foreach ($id_array as $one_id) {
			if (self::setLdapParams($one_id)) {
				//OCP\Util::writeLog('contacts_ldap', __METHOD__.' Connector OK', \OC_Log::DEBUG);
				$info = self::ldapFindMultiple(
					$this->ldapParams['ldapbasednsearch'],
					$this->ldapParams['ldapfilter'],
					$this->connector->getLdapEntries(),
					isset($options['offset']) ? $options['offset'] : null,
					isset($options['limit']) ? $options['limit'] : null
				);
				for ($i=0; $i<$info["count"]; $i++) {
					$a_card = $this->connector->ldapToVCard($info[$i]);
					$cards[] = self::getSabreFormatCard($addressbookid, $a_card);
				}
				//OCP\Util::writeLog('contacts_ldap', __METHOD__.' counts '.count($cards), \OC_Log::DEBUG);
			}
		}
		return $cards;
	}
	
	/**
	 * Returns a specfic contact.
	 *
	 * Same as getContacts except that either 'carddata' or 'vcard' is mandatory.
	 *
	 * @param string $addressbookid
	 * @param mixed $id
	 * @return array|bool
	 */
	public function getContact($addressbookid, $ids, array $options = array()) {
		if (!is_array($ids)) {
			$a_ids = array($ids);
		} else {
			$a_ids = $ids;
		}
		
		$cards = array();
		$toReturn = false;
		if (self::setLdapParams($addressbookid)) {
			foreach ($a_ids as $id) {
				$cid = str_replace(".vcf", "", $id);
				if (ldap_explode_dn(base64_decode($cid),0) == false) {
					$ldifEntry = $this->connector->getLdifEntry("X-URI", null);
					$filter = "";
					if (isset($ldifEntry[0]['unassigned'])) {
						$filter = $this->connector->getUnassignedVCardProperty() . "=X-URI:" . $cid ."*";
					} else {
						$filter = $ldifEntry[0]['name'] . "=" . $cid ."*";
					}
					$card = self::ldapFindOne($this->ldapParams['ldapbasednsearch'],
																	$filter,
																	$this->connector->getLdapEntries());
				} else {
					$card = self::ldapFindOne(base64_decode($cid),
																	$this->ldapParams['ldapfilter'],
																	$this->connector->getLdapEntries());
				}
			}
			if ($card != null) {
				return self::getSabreFormatCard($addressbookid, $this->connector->ldapToVCard($card));
			}
		}
		return false;
	}

	/**
	 * @brief construct a vcard in Sabre format
	 * @param integer $aid Addressbook Id
	 * @param OC_VObject $card VCard
	 * @return array
	 */
	public static function getSabreFormatCard($aid, $vcard) {
		/*
		 * array return format :
     * array( 'id' => 'bbcca2d1535', 
     *        'permissions' => 32, 
     *        'displayname' => 'Jane Doe', 
     *        'carddata' => $data)
		 */
		$FN = (string)$vcard->FN;
		$UID = (string)$vcard->UID;
		$REV = (string)$vcard->REV;
		if (isset($vcard->{'X-URI'})) {
			$URI = (string)$vcard->{'X-URI'};
		} else if (isset($vcard->UID)) {
			$URI = (string)$vcard->UID.'.vcf';
		} else {
			$URI = (string)$vcard->{'X-LDAP-DN'};
		}
		return array('id' => $UID,
								 'permissions' => \OCP\PERMISSION_ALL,
								 'displayname' => $FN,
								 'carddata' => $vcard->serialize(),
								 'uri' => $URI,
								 'lastmodified' => $REV);
	}

	/**
	 * Creates a new contact
	 *
	 * @param string $addressbookid
	 * @param VCard|string $carddata
	 * @return string|bool The identifier for the new contact or false on error.
	 */
	public function createContact($addressbookid, $contact, array $options = array()) {

		$uri = isset($options['uri']) ? $options['uri'] : null;
		
		$datetime = new \DateTime; 
		$contact->REV = $datetime->format(\DateTime::W3C);
		
		// 2014/02/13 Sometimes, a card is created without a name (I don't like that)...
		if (!isset($contact->N)) {
			$generated = "nocn-".rand(0, 65535);
			$contact->N = $generated;
			$contact->FN = $generated;
		}

		if(!$contact instanceof VCard) {
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
			OCP\Util::writeLog('contacts', __METHOD__ . ' ' .
				'Error validating vcard: ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}

		self::setLdapParams($addressbookid);
		
		self::ldapCreateAndBindConnection();
		$newDN = $this->connector->getLdapId() . "=" . $contact->FN . "," . $this->ldapParams['ldapbasednmodify'];
		$contact->{'X-LDAP-DN'} = base64_encode($newDN);
		if ($uri!=null) {
			$contact->{'X-URI'} = $uri;
		} else {
			$contact->{'X-URI'} = $contact->{'UID'}.".vcf";
		}
				
		$ldifEntries = $this->connector->VCardToLdap($contact);
		
		// Inserts the new card
		$cardId = self::ldapAdd($newDN, $ldifEntries);
		if ($cardId) {
			self::ldapCloseConnection();
			return $contact->UID;
		} else {
			self::ldapCloseConnection();
			return false;
		}
	}

	/**
	 * Updates a contact
	 *
	 * @param string $addressbookid
	 * @param mixed $id
	 * @param string $carddata
	 * @return bool
	 */
	public function updateContact($addressbookid, $id, $carddata, array $options = array()) {
		if(!$carddata instanceof VCard) {
			try {
				$vcard = \Sabre\VObject\Reader::read($carddata);
			} catch(\Exception $e) {
				\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
				return false;
			}
		} else {
			$vcard = $carddata;
		}
		
		try {
			$vcard->validate(VCard::REPAIR|VCard::UPGRADE);
		} catch (\Exception $e) {
			OCP\Util::writeLog('contacts', __METHOD__ . ' ' .
				'Error validating vcard: ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}
		//$vcard->REV = (new \DateTime)->format(\DateTime::W3C);
		
		if (!is_array($id)) {
			$a_ids = array($id);
		} else {
			$a_ids = $id;
		}

		self::setLdapParams($addressbookid);
		self::ldapCreateAndBindConnection();
		$ldifEntries = $this->connector->VCardToLdap($vcard);
		foreach ($a_ids as $cid) {
			// Never EVER modify an Ldap:dn nor a VCard:UID
			if (isset($vcard->{'X-LDAP-DN'})) {
				$dn = base64_decode($vcard->{'X-LDAP-DN'});
			} else {
				// A little bit complicated but hopefully, we won't often go into this
				$tmpCard = self::getContact($addressbookid, array($cid));
				$tmpVCard = \Sabre\VObject\Reader::read($tmpCard['carddata']);
				$dn = base64_decode($tmpVCard->{'X-LDAP-DN'});
			}
			// Updates the existing card
			$ldifSource = self::ldapFindOne($dn, $this->ldapParams['ldapfilter'], $this->connector->getLdapEntries());
			$this->connector->insertEmptyEntries($ldifSource, $ldifEntries);
			$result = self::ldapUpdate($dn, $ldifEntries);
		}
		self::ldapCloseConnection();
		return $result;
	}

	/**
	 * Deletes a contact
	 *
	 * @param string $addressbookid
	 * @param mixed $id
	 * @return bool
	 */
	public function deleteContact($addressbookid, $id, array $options = array()) {
		self::setLdapParams($addressbookid);
		self::ldapCreateAndBindConnection();
		if (is_array($id)) {
			$card = self::getContact($addressbookid, $id);
		} else {
			$card = self::getContact($addressbookid, array($id));
		}
		if ($card) {
			$vcard = \Sabre\VObject\Reader::read($card['carddata']);
			$decodedId = base64_decode($vcard->{'X-LDAP-DN'});
			// Deletes the existing card
			$result = self::ldapDelete($decodedId);
			self::ldapCloseConnection();
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * @brief Get the last modification time for a contact.
	 *
	 * Must return a UNIX time stamp or null if the backend
	 * doesn't support it.
	 *
	 * @param string $addressbookid
	 * @param mixed $id
	 * @returns int | null
	 */
	public function lastModifiedContact($addressbookid, $id) {
		$contact = getContact($addressbookid, $id);
		if ($contact != null) {
			return $contact['lastmodified'];
		} else {
			return null;
		}
	}
	
	/**
	 * @brief sets the list of ldap addressbooks in the preferences
	 * with the list given in parameter
	 * @param the new list
	 * @returns result|false
	 */
	protected function setAddressbookList(array $addressbookList) {
		$key = $this->name . "_list";
		$data = json_encode($addressbookList);
		
		return $data
			? \OCP\Config::setUserValue($this->userid, 'contacts', $key, $data)
			: false;
	}
	
	/**
	 * @brief gets the list of ldap addressbooks in the preferences
	 * returns array()
	 */
	protected function getAddressbookList() {
		$key = $this->name . "_list";
		$data = \OCP\Config::getUserValue($this->userid, 'contacts', $key, false);
		
		return $data ? json_decode($data) : array();
	}

	public function getSearchProvider($addressbook) {
		return new \OCA\Contacts\AddressbookProvider($addressbook);
	}
}
