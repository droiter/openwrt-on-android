<?php
/**
 * ownCloud - AddressbookProvider
 *
 * @author Thomas Tanghus
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

namespace OCA\Contacts;
use OCA\Contacts\Utils\JSONSerializer;
use OCA\Contacts\Utils\Properties;
use OCA\Contacts\Utils\TemporaryPhoto;
use OCA\Contacts\VObject\VCard;

/**
 * This class manages our addressbooks.
 * TODO: Port this to use the new backend
 */
class AddressbookProvider implements \OCP\IAddressBook {

	const CONTACT_TABLE = '*PREFIX*contacts_cards';
	const PROPERTY_TABLE = '*PREFIX*contacts_cards_properties';
	const ADDRESSBOOK_TABLE = '*PREFIX*contacts_addressbooks';

	/**
	 * Addressbook id
	 * @var integer
	 */
	public $id;
	
	/**
	 * Addressbook info array
	 * @var AddressBook
	 */
	public $addressBook;

	/**
	 * Constructor
	 * @param AddressBook $addressBook
	 */
	public function __construct($addressBook) {
		$this->addressBook = $addressBook;
		$this->app = new App();
	}
	
	public function getAddressbook() {
		return $this->addressBook;
	}
	
	/**
	* @return string defining the technical unique key
	*/
	public function getKey() {
		$metaData = $this->addressBook->getMetaData();
		return $metaData['backend'].':'.$metaData['id'];
	}

	/**
	* In comparison to getKey() this function returns a human readable (maybe translated) name
	* @return mixed
	*/
	public function getDisplayName() {
		return $this->addressBook->getDisplayName();
	}

	/**
	* @return mixed
	*/
	public function getPermissions() {
		return $this->addressBook->getPermissions();
	}

	/**
	* @param $pattern
	* @param $searchProperties
	* @param $options
	* @return array|false
	*/
	public function search($pattern, $searchProperties, $options) {
		$propTable = self::PROPERTY_TABLE;
		$contTable = self::CONTACT_TABLE;
		$addrTable = self::ADDRESSBOOK_TABLE;
		$results = array();

		/**
		 * This query will fetch all contacts which match the $searchProperties
		 * It will look up the addressbookid of the contact and the user id of the owner of the contact app
		 */
		$query = <<<SQL
			SELECT
				DISTINCT
				`$propTable`.`contactid`,
				`$contTable`.`addressbookid`,
				`$addrTable`.`userid`

			FROM
				`$propTable`
			INNER JOIN
				`$contTable`
			ON `$contTable`.`id` = `$propTable`.`contactid`
  				INNER JOIN `$addrTable`
			ON `$addrTable`.id = `$contTable`.addressbookid
			WHERE
				`$contTable`.addressbookid = ? AND
				(
SQL;

		$params = array();
		$meta = $this->addressBook->getMetaData();
		$params[] = $meta['id'];
		foreach ($searchProperties as $property) {
			$params[] = $property;
			$params[] = '%' . $pattern . '%';
			$query .= '(`name` = ? AND `value` LIKE ?) OR ';
		}
		$query = substr($query, 0, strlen($query) - 4);
		$query .= ')';

		$stmt = \OCP\DB::prepare($query);
		$result = $stmt->execute($params);
		if (\OCP\DB::isError($result)) {
			\OCP\Util::writeLog('contacts', __METHOD__ . 'DB error: ' . \OC_DB::getErrorMessage($result),
				\OCP\Util::ERROR);
			return false;
		}
		while ($row = $result->fetchRow()) {
			$id = $row['contactid'];
			$addressbookKey = $row['addressbookid'];
			// Check if we are the owner of the contact
			if ($row['userid'] !== \OCP\User::getUser()) {
				// we aren't the owner of the contact
				try {
					// it is possible that the contact is shared with us
					// if so, $contact will be an object
					// if not getContact will throw an Exception
					$contact = $this->app->getContact('shared', $addressbookKey, $id);
				} catch (\Exception $e){
					// the contact isn't shared with us
					$contact = null;
				}
			} else {
				// We are the owner of the contact
				// thus we can easily fetch it
				$contact = $this->app->getContact('local', $addressbookKey, $id);
			}
			if ($contact !== null) {
				$j = JSONSerializer::serializeContact($contact);
				$j['data']['id'] = $id;
				if (isset($contact->PHOTO)) {
					$url = \OCP\Util::linkToRoute('contacts_contact_photo',
						array(
							'backend' => $contact->getBackend()->name,
							'addressBookId' => $addressbookKey,
							'contactId' => $contact->getId()
						));
					$url = \OC_Helper::makeURLAbsolute($url);
					$j['data']['PHOTO'] = "VALUE=uri:$url";
				}
				$results[] = $this->convertToSearchResult($j);
			}
		}
		return $results;
	}

	/**
	* @param $properties
	* @return mixed
	*/
	public function createOrUpdate($properties) {
		$id = null;

		/**
		 * @var \OCA\Contacts\VObject\VCard
		 */
		$vcard = null;
		if(array_key_exists('id', $properties)) {
			// TODO: test if $id belongs to this addressbook
			$id = $properties['id'];
			// TODO: Test $vcard
			$vcard = $this->addressBook->getChild($properties['id']);
			foreach(array_keys($properties) as $name) {
				if(isset($vcard->{$name})) {
					unset($vcard->{$name});
				}
			}
		} else {
			$vcard = \Sabre\VObject\Component::create('VCARD');
			$uid = substr(md5(rand().time()), 0, 10);
			$vcard->add('UID', $uid);
			try {
				$id = $this->addressBook->addChild($vcard);
			} catch(\Exception $e) {
				\OCP\Util::writeLog('contacts', __METHOD__ . ' ' . $e->getMessage(), \OCP\Util::ERROR);
				return false;
			}
		}

		foreach($properties as $name => $value) {
			switch($name) {
				case 'ADR':
				case 'N':
					if(is_array($value)) {
						$property = \Sabre\VObject\Property::create($name);
						$property->setParts($value);
						$vcard->add($property);
					} else {
						$vcard->{$name} = $value;
					}
					break;
				case 'BDAY':
					// TODO: try/catch
					$date = New \DateTime($value);
					$vcard->BDAY = $date->format('Y-m-d');
					$vcard->BDAY->VALUE = 'DATE';
					break;
				case 'EMAIL':
				case 'TEL':
				case 'IMPP': // NOTE: We don't know if it's GTalk, Jabber etc. only the protocol
				case 'URL':
					if(is_array($value)) {
						foreach($value as $val) {
							$vcard->add($name, strip_tags($val));
						}
					} else {
						$vcard->add($name, strip_tags($value));
					}
				default:
					$vcard->{$name} = $value;
					break;
			}
		}

		try {
			VCard::edit($id, $vcard);
		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__ . ' ' . $e->getMessage(), \OCP\Util::ERROR);
			return false;
		}
		
		$asarray = VCard::structureContact($vcard);
		$asarray['id'] = $id;
		return $asarray;
	}

	/**
	* @param $id
	* @return mixed
	*/
	public function delete($id) {
		try {
			$query = 'SELECT COUNT(*) as `count` FROM `*PREFIX*contacts_cards` WHERE `id` = ? AND `addressbookid` = ?';
			$stmt = \OCP\DB::prepare($query);
			$result = $stmt->execute(array($id, $this->id));
			if (\OCP\DB::isError($result)) {
				\OCP\Util::writeLog('contacts', __METHOD__ . 'DB error: ' . \OC_DB::getErrorMessage($result),
					\OCP\Util::ERROR);
				return false;
			}
			if((int)$result['count'] === 0) {
				\OCP\Util::writeLog('contacts', __METHOD__
					. 'Contact with id ' . $id . 'doesn\'t belong to addressbook with id ' . $this->id, 
					\OCP\Util::ERROR);
				return false;
			}
		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__ . ', exception: ' . $e->getMessage(), 
				\OCP\Util::ERROR);
			return false;
		}
		return VCard::delete($id);
	}

	/**
	 * @param $j
	 * @return array
	 */
	private function convertToSearchResult($j) {
		$data = $j['data'];
		$result = array();
		foreach( $data as $key => $d) {
			$d = $data[$key];
			if (in_array($key, Properties::$multiProperties)) {
				$result[$key] = array_map(function($v){
					return $v['value'];
				}, $d);
			} else {
				if (is_array($d)) {
					$result[$key] = $d[0]['value'];
				} else {
					$result[$key] = $d;
				}
			}
		}

		return $result;
	}

}
