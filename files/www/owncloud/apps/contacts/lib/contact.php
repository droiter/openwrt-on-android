<?php
/**
 * ownCloud - Contact object
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

use Sabre\VObject\Property,
	OCA\Contacts\Utils\Properties;

/**
 * Subclass this class or implement IPIMObject interface for PIM objects
 */

class Contact extends VObject\VCard implements IPIMObject {

	/**
	 * The name of the object type in this case VCARD.
	 *
	 * This is used when serializing the object.
	 *
	 * @var string
	 */
	public $name = 'VCARD';

	/**
	 * @brief language object
	 *
	 * @var OC_L10N
	 */
	public static $l10n;

	protected $props = array();

	/**
	 * Create a new Contact object
	 *
	 * @param AddressBook $parent
	 * @param Backend\AbstractBackend $backend
	 * @param mixed $data
	 */
	public function __construct($parent, $backend, $data = null) {
		self::$l10n = $parent::$l10n;
		//\OCP\Util::writeLog('contacts', __METHOD__ . ' , data: ' . print_r($data, true), \OCP\Util::DEBUG);
		$this->props['parent'] = $parent;
		$this->props['backend'] = $backend;
		$this->props['retrieved'] = false;
		$this->props['saved'] = false;

		if (!is_null($data)) {
			if ($data instanceof VObject\VCard) {
				foreach ($data->children as $child) {
					$this->add($child);
				}
				$this->setRetrieved(true);
			} elseif (is_array($data)) {
				foreach ($data as $key => $value) {
					switch ($key) {
						case 'id':
							$this->props['id'] = $value;
							break;
						case 'permissions':
							$this->props['permissions'] = $value;
							break;
						case 'lastmodified':
							$this->props['lastmodified'] = $value;
							break;
						case 'uri':
							$this->props['uri'] = $value;
							break;
						case 'carddata':
							$this->props['carddata'] = $value;
							$this->retrieve();
							break;
						case 'vcard':
							$this->props['vcard'] = $value;
							$this->retrieve();
							break;
						case 'displayname':
						case 'fullname':
							if(is_string($value)) {
								$this->props['displayname'] = $value;
								$this->FN = $value;
								// Set it to saved again as we're not actually changing anything
								$this->setSaved();
							}
							break;
					}
				}
			}
		}
	}

	/**
	 * @return array|null
	 */
	public function getMetaData() {
		if (!$this->hasPermission(\OCP\PERMISSION_READ)) {
			throw new \Exception(self::$l10n->t('You do not have permissions to see this contact'), 403);
		}
		if (!isset($this->props['displayname'])) {
			if (!$this->retrieve()) {
				\OCP\Util::writeLog('contacts', __METHOD__.' error reading: '.print_r($this->props, true), \OCP\Util::ERROR);
				return null;
			}
		}
		return array(
			'id' => $this->getId(),
			'displayname' => $this->getDisplayName(),
			'permissions' => $this->getPermissions(),
			'lastmodified' => $this->lastModified(),
			'owner' => $this->getOwner(),
			'parent' => $this->getParent()->getId(),
			'backend' => $this->getBackend()->name,
		);
	}

	/**
	 * Get a unique key combined of backend name, address book id and contact id.
	 *
	 * @return string
	 */
	public function combinedKey() {
		return $this->getBackend()->name . '::' . $this->getParent()->getId() . '::' . $this->getId();
	}

	/**
	 * @return string|null
	 */
	public function getOwner() {
		return isset($this->props['owner'])
			? $this->props['owner']
			: $this->getParent()->getOwner();
	}

	/**
	 * @return string|null
	 */
	public function getId() {
		return isset($this->props['id']) ? $this->props['id'] : null;
	}

	/**
	 * @return string|null
	 */
	public function getDisplayName() {
		if (!$this->hasPermission(\OCP\PERMISSION_READ)) {
			throw new \Exception(self::$l10n->t('You do not have permissions to see this contact'), 403);
		}
		return isset($this->props['displayname'])
			? $this->props['displayname']
			: (isset($this->FN) ? $this->FN : null);
	}

	/**
	 * @return string|null
	 */
	public function getURI() {
		return isset($this->props['uri']) ? $this->props['uri'] : null;
	}

	/**
	 * @return string|null
	 * TODO: Cache result.
	 */
	public function getETag() {
		$this->retrieve();
		return md5($this->serialize());
	}

	/**
	 * If this object is part of a collection return a reference
	 * to the parent object, otherwise return null.
	 * @return IPIMObject|null
	 */
	public function getParent() {
		return $this->props['parent'];
	}

	public function getBackend() {
		return $this->props['backend'];
	}

	/** CRUDS permissions (Create, Read, Update, Delete, Share)
	 *
	 * @return integer
	 */
	public function getPermissions() {
		return isset($this->props['permissions'])
			? $this->props['permissions']
			: $this->getParent()->getPermissions();
	}

	/**
	 * @param integer $permission
	 * @return bool
	 */
	public function hasPermission($permission) {
		return $this->getPermissions() & $permission;
	}

	/**
	 * Save the address book data to backend
	 * FIXME
	 *
	 * @param array $data
	 * @return bool
	 */
/*	public function update(array $data) {

		foreach($data as $key => $value) {
			switch($key) {
				case 'displayname':
					$this->addressBookInfo['displayname'] = $value;
					break;
				case 'description':
					$this->addressBookInfo['description'] = $value;
					break;
			}
		}
		return $this->props['backend']->updateContact(
			$this->getParent()->getId(),
			$this->getId(),
			$this
		);
	}
*/
	/**
	 * Delete the data from backend
	 *
	 * FIXME: Should be removed as it could leave the parent with a dataless object.
	 *
	 * @return bool
	 */
	public function delete() {
		if (!$this->hasPermission(\OCP\PERMISSION_DELETE)) {
			throw new \Exception(self::$l10n->t('You do not have permissions to delete this contact'), 403);
		}
		return $this->props['backend']->deleteContact(
			$this->getParent()->getId(),
			$this->getId()
		);
	}

	/**
	 * Save the contact data to backend
	 *
	 * @return bool
	 */
	public function save($force = false) {
		if (!$this->hasPermission(\OCP\PERMISSION_UPDATE)) {
			throw new \Exception(self::$l10n->t('You do not have permissions to update this contact'), 403);
		}
		if ($this->isSaved() && !$force) {
			\OCP\Util::writeLog('contacts', __METHOD__.' Already saved: ' . print_r($this->props, true), \OCP\Util::DEBUG);
			return true;
		}

		if (isset($this->FN)) {
			$this->props['displayname'] = (string)$this->FN;
		}

		if ($this->getId()) {
			if (!$this->getBackend()->hasContactMethodFor(\OCP\PERMISSION_UPDATE)) {
				throw new \Exception(self::$l10n->t('The backend for this contact does not support updating it'), 501);
			}
			if ($this->getBackend()
				->updateContact(
					$this->getParent()->getId(),
					$this->getId(),
					$this
				)
			) {
				$this->props['lastmodified'] = time();
				$this->setSaved(true);
				return true;
			} else {
				return false;
			}
		} else {
			if (!$this->getBackend()->hasContactMethodFor(\OCP\PERMISSION_CREATE)) {
				throw new \Exception(self::$l10n->t('This backend does not support adding contacts'), 501);
			}
			$this->props['id'] = $this->getBackend()->createContact(
				$this->getParent()->getId(), $this
			);
			$this->setSaved(true);
			return $this->getId() !== false;
		}
	}

	/**
	 * Get the data from the backend
	 * FIXME: Clean this up and make sure the logic is OK.
	 *
	 * @return bool
	 */
	public function retrieve() {
		if ($this->isRetrieved() || count($this->children) > 1) {
			//\OCP\Util::writeLog('contacts', __METHOD__. ' children', \OCP\Util::DEBUG);
			return true;
		} else {
			$data = null;
			if(isset($this->props['vcard'])
				&& $this->props['vcard'] instanceof VObject\VCard) {
				foreach($this->props['vcard']->children() as $child) {
					$this->add($child);
					if($child->name === 'FN') {
						$this->props['displayname']
							= strtr($child->value, array('\,' => ',', '\;' => ';', '\\\\' => '\\'));
					}
				}
				$this->setRetrieved(true);
				$this->setSaved(true);
				//$this->children = $this->props['vcard']->children();
				unset($this->props['vcard']);
				return true;
			} elseif (!isset($this->props['carddata'])) {
				$result = $this->props['backend']->getContact(
					$this->getParent()->getId(),
					$this->getId()
				);
				if ($result) {
					if (isset($result['vcard'])
						&& $result['vcard'] instanceof VObject\VCard) {
						foreach ($result['vcard']->children() as $child) {
							$this->add($child);
						}
						$this->setRetrieved(true);
						return true;
					} elseif (isset($result['carddata'])) {
						// Save internal values
						$data = $result['carddata'];
						$this->props['carddata'] = $result['carddata'];
						$this->props['lastmodified'] = isset($result['lastmodified'])
							? $result['lastmodified']
							: null;
						$this->props['displayname'] = $result['displayname'];
						$this->props['permissions'] = $result['permissions'];
					} else {
						\OCP\Util::writeLog('contacts', __METHOD__
							. ' Could not get vcard or carddata: '
							. $this->getId()
							. print_r($result, true), \OCP\Util::DEBUG);
						return false;
					}
				} else {
					\OCP\Util::writeLog('contacts', __METHOD__.' Error getting contact: ' . $this->getId(), \OCP\Util::DEBUG);
				}
			} elseif (isset($this->props['carddata'])) {
				$data = $this->props['carddata'];
			}
			try {
				$obj = \Sabre\VObject\Reader::read(
					$data,
					\Sabre\VObject\Reader::OPTION_IGNORE_INVALID_LINES
				);
				if ($obj) {
					foreach ($obj->children as $child) {
						$this->add($child);
					}
					$this->setRetrieved(true);
					$this->setSaved(true);
				} else {
					\OCP\Util::writeLog('contacts', __METHOD__.' Error reading: ' . print_r($data, true), \OCP\Util::DEBUG);
					return false;
				}
			} catch (\Exception $e) {
				\OCP\Util::writeLog('contacts', __METHOD__ .
					' Error parsing carddata  for: ' . $this->getId() . ' ' . $e->getMessage(),
						\OCP\Util::ERROR);
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the PHOTO or LOGO
	 *
	 * @return \OCP\Image|null
	 */
	public function getPhoto() {
		$image = new \OCP\Image();

		if (isset($this->PHOTO) && $image->loadFromBase64((string)$this->PHOTO)) {
			return $image;
		} elseif (isset($this->LOGO) && $image->loadFromBase64((string)$this->LOGO)) {
			return $image;
		}
	}

	/**
	 * Set the contact photo.
	 *
	 * @param \OCP\Image $photo
	 */
	public function setPhoto(\OCP\Image $photo) {
		// For vCard 3.0 the type must be e.g. JPEG or PNG
		// For version 4.0 the full mimetype should be used.
		// https://tools.ietf.org/html/rfc2426#section-3.1.4
		if (strval($this->VERSION) === '4.0') {
			$type = $photo->mimeType();
		} else {
			$type = explode('/', $photo->mimeType());
			$type = strtoupper(array_pop($type));
		}
		if (isset($this->PHOTO)) {
			$property = $this->PHOTO;
			if (!$property) {
				return false;
			}
			$property->setValue(strval($photo));
			$property->parameters = array();
			$property->parameters[]
				= new \Sabre\VObject\Parameter('ENCODING', 'b');
			$property->parameters[]
				= new \Sabre\VObject\Parameter('TYPE', $photo->mimeType());
			$this->PHOTO = $property;
		} else {
			$this->add('PHOTO',
				strval($photo), array('ENCODING' => 'b',
				'TYPE' => $type));
			// TODO: Fix this hack
			$this->setSaved(false);
		}

		return true;

	}

	/**
	* Get a property index in the contact by the checksum of its serialized value
	*
	* @param string $checksum An 8 char m5d checksum.
	* @return \Sabre\VObject\Property Property by reference
	* @throws An exception with error code 404 if the property is not found.
	*/
	public function getPropertyIndexByChecksum($checksum) {
		$this->retrieve();
		$idx = 0;
		foreach ($this->children as $i => &$property) {
			if (substr(md5($property->serialize()), 0, 8) == $checksum ) {
				return $idx;
			}
			$idx += 1;
		}
		throw new \Exception(self::$l10n->t('Property not found'), 404);
	}

	/**
	* Get a property by the checksum of its serialized value
	*
	* @param string $checksum An 8 char m5d checksum.
	* @return \Sabre\VObject\Property Property by reference
	* @throws An exception with error code 404 if the property is not found.
	*/
	public function getPropertyByChecksum($checksum) {
		$this->retrieve();
		foreach ($this->children as $i => &$property) {
			if (substr(md5($property->serialize()), 0, 8) == $checksum ) {
				return $property;
			}
		}
		throw new \Exception(self::$l10n->t('Property not found'), 404);
	}

	/**
	* Delete a property by the checksum of its serialized value
	* It is up to the caller to call ->save()
	*
	* @param string $checksum An 8 char m5d checksum.
	* @throws @see getPropertyByChecksum
	*/
	public function unsetPropertyByChecksum($checksum) {
		$idx = $this->getPropertyIndexByChecksum($checksum);
		unset($this->children[$idx]);
		$this->setSaved(false);
	}

	/**
	* Set a property by the checksum of its serialized value
	* It is up to the caller to call ->save()
	*
	* @param string $checksum An 8 char m5d checksum.
	* @param string $name Property name
	* @param mixed $value
	* @param array $parameters
	* @throws @see getPropertyByChecksum
	* @return string new checksum
	*/
	public function setPropertyByChecksum($checksum, $name, $value, $parameters=array()) {
		if ($checksum === 'new') {
			$property = Property::create($name);
			$this->add($property);
		} else {
			$property = $this->getPropertyByChecksum($checksum);
		}
		switch ($name) {
			case 'EMAIL':
				$value = strtolower($value);
				$property->setValue($value);
				break;
			case 'ADR':
				if(is_array($value)) {
					$property->setParts($value);
				} else {
					$property->setValue($value);
				}
				break;
			case 'IMPP':
				if (is_null($parameters) || !isset($parameters['X-SERVICE-TYPE'])) {
					throw new \InvalidArgumentException(self::$l10n->t(' Missing IM parameter for: ') . $name. ' ' . $value, 412);
				}
				$serviceType = $parameters['X-SERVICE-TYPE'];
				if (is_array($serviceType)) {
					$serviceType = $serviceType[0];
				}
				$impp = Utils\Properties::getIMOptions($serviceType);
				if (is_null($impp)) {
					throw new \UnexpectedValueException(self::$l10n->t('Unknown IM: ') . $serviceType, 415);
				}
				$value = $impp['protocol'] . ':' . $value;
				$property->setValue($value);
				break;
			default:
				\OCP\Util::writeLog('contacts', __METHOD__.' adding: '.$name. ' ' . $value, \OCP\Util::DEBUG);
				$property->setValue($value);
				break;
		}
		$this->setParameters($property, $parameters, true);
		$this->setSaved(false);
		return substr(md5($property->serialize()), 0, 8);
	}

	/**
	* Set a property by the property name.
	* It is up to the caller to call ->save()
	*
	* @param string $name Property name
	* @param mixed $value
	* @param array $parameters
	* @return bool
	*/
	public function setPropertyByName($name, $value, $parameters=array()) {
		// TODO: parameters are ignored for now.
		switch ($name) {
			case 'BDAY':
				try {
					$date = New \DateTime($value);
				} catch(\Exception $e) {
					\OCP\Util::writeLog('contacts',
						__METHOD__.' DateTime exception: ' . $e->getMessage(),
						\OCP\Util::ERROR
					);
					return false;
				}
				$value = $date->format('Y-m-d');
				$this->BDAY = $value;
				$this->BDAY->add('VALUE', 'DATE');
				//\OCP\Util::writeLog('contacts', __METHOD__.' BDAY: '.$this->BDAY->serialize(), \OCP\Util::DEBUG);
				break;
			case 'CATEGORIES':
			case 'N':
			case 'ORG':
				$property = $this->select($name);
				if (count($property) === 0) {
					$property = \Sabre\VObject\Property::create($name);
					$this->add($property);
				} else {
					// Actually no idea why this works
					$property = array_shift($property);
				}
				if (is_array($value)) {
					$property->setParts($value);
				} else {
					$this->{$name} = $value;
				}
				break;
			default:
				\OCP\Util::writeLog('contacts', __METHOD__.' adding: '.$name. ' ' . $value, \OCP\Util::DEBUG);
				$this->{$name} = $value;
				break;
		}
		$this->setSaved(false);
		return true;
	}

	protected function setParameters($property, $parameters, $reset = false) {
		if (!$parameters) {
			return;
		}

		if ($reset) {
			$property->parameters = array();
		}
		//debug('Setting parameters: ' . print_r($parameters, true));
		foreach ($parameters as $key => $parameter) {
			//debug('Adding parameter: ' . $key);
			if (is_array($parameter)) {
				foreach ($parameter as $val) {
					if (is_array($val)) {
						foreach ($val as $val2) {
							if (trim($key) && trim($val2)) {
								//debug('Adding parameter: '.$key.'=>'.print_r($val2, true));
								$property->add($key, strip_tags($val2));
							}
						}
					} else {
						if (trim($key) && trim($val)) {
							//debug('Adding parameter: '.$key.'=>'.print_r($val, true));
							$property->add($key, strip_tags($val));
						}
					}
				}
			} else {
				if (trim($key) && trim($parameter)) {
					//debug('Adding parameter: '.$key.'=>'.print_r($parameter, true));
					$property->add($key, strip_tags($parameter));
				}
			}
		}
	}

	public function lastModified() {
		if (!isset($this->props['lastmodified']) && !$this->isRetrieved()) {
			$this->retrieve();
		}
		return isset($this->props['lastmodified'])
			? $this->props['lastmodified']
			: null;
	}

	/**
	 * Merge in data from a multi-dimentional array
	 *
	 * NOTE: The data has actually already been merged client side!
	 * NOTE: The only properties coming from the web client are the ones
	 * defined in \OCA\Contacts\Utils\Properties::$indexProperties and
	 * UID is skipped for obvious reasons, and PHOTO is currently not updated.
	 * The data array has this structure:
	 *
	 * array(
	 * 	'EMAIL' => array(array('value' => 'johndoe@example.com', 'parameters' = array('TYPE' => array('HOME','VOICE'))))
	 * );
	 * @param array $data
	 * @return bool
	 */
	public function mergeFromArray(array $data) {
		foreach ($data as $name => $properties) {
			if (in_array($name, array('PHOTO', 'UID'))) {
				continue;
			}
			if (!is_array($properties)) {
				\OCP\Util::writeLog('contacts', __METHOD__.' not an array?: ' .$name. ' '.print_r($properties, true), \OCP\Util::DEBUG);
			}
			if (in_array($name, Utils\Properties::$multiProperties)) {
				unset($this->{$name});
			}
			foreach ($properties as $parray) {
				\OCP\Util::writeLog('contacts', __METHOD__.' adding: ' .$name. ' '.print_r($parray['value'], true) . ' ' . print_r($parray['parameters'], true), \OCP\Util::DEBUG);
				if (in_array($name, Utils\Properties::$multiProperties)) {
					// TODO: wrap in try/catch, check return value
					$this->setPropertyByChecksum('new', $name, $parray['value'], $parray['parameters']);
				} else {
					// TODO: Check return value
					if (!isset($this->{$name})) {
						$this->setPropertyByName($name, $parray['value'], $parray['parameters']);
					}
				}
			}
		}
		$this->setSaved(false);
		return true;
	}

	/**
	 * Merge in data from another VCard
	 * Used on import if a matching UID is found. Returns true if any updates
	 * take place, otherwise false.
	 *
	 * @param VCard $vcard
	 * @return bool
	 */
	public function mergeFromVCard(VCard $vcard) {
		$updated = false;
		foreach ($vcard->children as $property) {
			if (in_array($property->name, array('REV', 'UID'))) {
				continue;
			}
			\OCP\Util::writeLog('contacts', __METHOD__.' merging: ' .$property->name, \OCP\Util::DEBUG);
			if (in_array($property->name, Utils\Properties::$multiProperties)) {
				$ownproperties = $this->select($property->name);
				if (count($ownproperties) === 0) {
					// We don't have any instances of this property, so just add it.
					$this->add($property);
					$updated = true;
					continue;
				} else {
					foreach ($ownproperties as $ownproperty) {
						if (strtolower($property->value) === strtolower($ownproperty->value)) {
							// We already have this property, so skip both loops
							continue 2;
						}
					}
					$this->add($property);
					$updated = true;
				}
			} else {
				if(!isset($this->{$property->name})) {
					$this->add($property);
					$updated = true;
				} else {
					$this->setPropertyByName($property->name, $property->value, $property->parameters);
				}
			}
		}

		$this->setSaved(!$updated);

		return $updated;
	}

	public function __get($key) {
		if (!$this->isRetrieved()) {
			$this->retrieve();
		}

		return parent::__get($key);
	}

	public function __isset($key) {
		if (!$this->isRetrieved()) {
			$this->retrieve();
		}

		return parent::__isset($key);
	}

	public function __set($key, $value) {
		if (!$this->isRetrieved()) {
			$this->retrieve();
		}
		parent::__set($key, $value);
		if ($key === 'FN') {
			$this->props['displayname'] = $value;
		}
		$this->setSaved(false);
	}

	public function __unset($key) {
		if (!$this->isRetrieved()) {
			$this->retrieve();
		}
		parent::__unset($key);
		if ($key === 'PHOTO') {
			Properties::cacheThumbnail(
				$this->getBackend()->name,
				$this->getParent()->getId(),
				$this->getId(),
				null,
				$this,
				array('remove' => true)
			);
		}
		$this->setSaved(false);
	}

	public function setRetrieved($state) {
		$this->props['retrieved'] = $state;
	}

	public function isRetrieved() {
		return $this->props['retrieved'];
	}

	public function setSaved($state = true) {
		$this->props['saved'] = $state;
	}

	public function isSaved() {
		return $this->props['saved'];
	}

	/**
	 * Generate an event to show in the calendar
	 *
	 * @return \Sabre\VObject\Component\VCalendar|null
	 */
	public function getBirthdayEvent() {
		if (!isset($this->BDAY)) {
			return;
		}
		$birthday = $this->BDAY;
		if ((string)$birthday) {
			$title = str_replace('{name}',
				strtr((string)$this->FN, array('\,' => ',', '\;' => ';')),
				App::$l10n->t('{name}\'s Birthday')
			);
			try {
				$date = new \DateTime($birthday);
			} catch(\Exception $e) {
				return;
			}
			$vevent = \Sabre\VObject\Component::create('VEVENT');
			$vevent->add('DTSTART');
			$vevent->DTSTART->setDateTime(
				$date,
				\Sabre\VObject\Property\DateTime::DATE
			);
			$vevent->add('DURATION', 'P1D');
			$vevent->{'UID'} = $this->UID;
			$vevent->{'RRULE'} = 'FREQ=YEARLY';
			$vevent->{'SUMMARY'} = $title . ' (' . $date->format('Y') . ')';
			$vcal = \Sabre\VObject\Component::create('VCALENDAR');
			$vcal->VERSION = '2.0';
			$appinfo = \OCP\App::getAppInfo('contacts');
			$appversion = \OCP\App::getAppVersion('contacts');
			$vcal->PRODID = '-//ownCloud//NONSGML '.$appinfo['name'].' '.$appversion.'//EN';
			$vcal->add($vevent);
			return $vcal;
		}
	}

}
