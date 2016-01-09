<?php
/**
 * ownCloud - Addressbook LDAP
 *
 * @author Nicolas Mora
 * @copyright 2013 Nicolas Mora mail@babelouest.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation
 * version 3 of the License
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
 
namespace OCA\Contacts\Connector;
use Sabre\VObject\Component;

class LdapConnector {
	
	public function __construct($xml_config) {
		try {
			//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.', setting xml config', \OCP\Util::DEBUG);
			$this->config_content = new \SimpleXMLElement($xml_config);
		} catch (Exception $e) {
			\OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.', error in setting xml config', \OCP\Util::DEBUG);
		}
	}
	private function convertDate ($ldapDate) {

		$tstamp = strtotime($ldapDate);
		$theDate = new \DateTime;
		$theDate->setTimestamp($tstamp);
		
		return $theDate;
	}
	/**
	 * @brief transform a ldap entry into an VCard object
	 *	for each ldap entry which is like "property: value"
	 *	to a VCard entry which is like "PROPERTY[;PARAMETER=param]:value"
	 * @param array $ldap_entry
	 * @return OC_VCard
	 */
	public function ldapToVCard($ldapEntry) {
		$vcard = \Sabre\VObject\Component::create('VCARD');
		$vcard->REV = $this->convertDate($ldapEntry['modifytimestamp'][0])->format(\DateTime::W3C);
		//error_log("modifytimestamp: ".$vcard->REV);
		$vcard->{'X-LDAP-DN'} = base64_encode($ldapEntry['dn']);
		// OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.' vcard is '.$vcard->serialize(), \OCP\Util::DEBUG);
		
		for ($i=0; $i<$ldapEntry["count"]; $i++) {
			// ldap property name : $ldap_entry[$i]
			$lProperty = $ldapEntry[$i];
			for ($j=0;$j<$ldapEntry[$lProperty]["count"];$j++){
				
				// What to do :
				// convert the ldap property into vcard property, type and position (if needed)
				// $v_params format: array('property' => property, 'type' => array(types), 'position' => position)
				$v_params = $this->getVCardProperty($lProperty);
				
				foreach ($v_params as $v_param) {
					
					if (isset($v_param['unassigned'])) {
						// if the value comes from the unassigned entry, it's a vcard property dumped
						try {
							$property = \Sabre\VObject\Reader::read($ldapEntry[$lProperty][$j]);
							$vcard->add($property);
						} catch (exception $e) {
						}
					} else {
						// Checks if a same kind of property already exists in the VCard (property and parameters)
						// if so, sets a property variable with the current data
						// else, creates a property variable
						$v_property = $this->getOrCreateVCardProperty($vcard, $v_param, $j);
						
						// modify the property with the new data
						if (strcasecmp($v_param['image'], 'true') == 0) {
							$this->updateVCardImageProperty($v_property, $ldapEntry[$lProperty][$j], $vcard->VERSION);
						} else {
							$this->updateVCardProperty($v_property, $ldapEntry[$lProperty][$j], $v_param['position']);
						}
					}
				}
			}
		}
		
		if (!isset($vcard->UID)) {
			$vcard->UID = base64_encode($ldapEntry['dn']);
		}
		return $vcard;
	}
	
	/**
	 * @brief returns the vcard property corresponding to the ldif parameter
	 * creates the property if it doesn't exists yet
	 * @param $vcard the vcard to get or create the properties with
	 * @param $v_param the parameter the find
	 * @param $index the position of the property in the vcard to find
	 */
	public function getOrCreateVCardProperty(&$vcard, $v_param, $index) {
		
		// looking for one
		//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.' entering '.$vcard->serialize(), \OCP\Util::DEBUG);
		$properties = $vcard->select($v_param['property']);
		$counter = 0;
		foreach ($properties as $property) {
			if ($v_param['type'] == null) {
				//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.' property '.$v_param['type'].' found', \OCP\Util::DEBUG);
				return $property;
			}
			foreach ($property->parameters as $parameter) {
				//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.' parameter '.$parameter->value.' <> '.$v_param['type'], \OCP\Util::DEBUG);
				if (!strcmp($parameter->value, $v_param['type'])) {
					//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.' parameter '.$parameter->value.' found', \OCP\Util::DEBUG);
					if ($counter==$index) {
						return $property;
					}
					$counter++;
				}
			}
		}
		
		// Property not found, creating one
		//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.', create one '.$v_param['property'].';TYPE='.$v_param['type'], \OCP\Util::DEBUG);
		$line = count($vcard->children) - 1;
		$property = \Sabre\VObject\Property::create($v_param['property']);
		$vcard->add($property);
		if ($v_param['type']!=null) {
			//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.', creating one '.$v_param['property'].';TYPE='.$v_param['type'], \OCP\Util::DEBUG);
			//\OC_Log::write('ldapconnector', __METHOD__.', creating one '.$v_param['property'].';TYPE='.$v_param['type'], \OC_Log::DEBUG);
			$property->parameters[] = new	\Sabre\VObject\Parameter('TYPE', ''.$v_param['type']);
			switch ($v_param['property']) {
				case "ADR":
					//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.', we have an address '.$v_param['property'].';TYPE='.$v_param['type'], \OCP\Util::DEBUG);
					$property->setValue(";;;;;;");
					break;
				case "FN":
					$property->setValue(";;;;");
					break;
			}
		}
		//OCP\Util::writeLog('ldap_vcard_connector', __METHOD__.' exiting '.$vcard->serialize(), \OCP\Util::DEBUG);
		return $property;
	}
	
	/**
	 * @brief modifies a vcard property array with the ldap_entry given in parameter at the given position
	 */
	public function updateVCardProperty(&$v_property, $ldap_entry, $position=null) {
		for ($i=0; $i<count($v_property); $i++) {
			if ($position != null) {
				$v_array = explode(";", $v_property[$i]);
				$v_array[intval($position)] = $ldap_entry;
				$v_property[$i]->setValue(implode(";", $v_array));
			} else {
				$v_property[$i]->setValue($ldap_entry);
			}
		}
	}
	
	/**
	 * @brief modifies a vcard property array with the image
	 */
	public function updateVCardImageProperty(&$v_property, $ldap_entry, $version) {
		for ($i=0; $i<count($v_property); $i++) {
			$image = new \OC_Image();
			$image->loadFromData($ldap_entry);
			if (strcmp($version, '4.0') == 0) {
				$type = $image->mimeType();
			} else {
				$arrayType = explode('/', $image->mimeType());
				$type = strtoupper(array_pop($arrayType));
			}
			$v_property[$i]->add('ENCODING', 'b');
			$v_property[$i]->add('TYPE', $type);
			$v_property[$i]->setValue($image->__toString());
		}
	}
	
	/**
	 * @brief gets the vcard property values from an ldif entry name
	 * @param $lProperty the ldif property name
	 * @return array('property' => property, 'type' => type, 'position' => position)
	 */
	public function getVCardProperty($lProperty) {
		$properties = array();
		if (strcmp($lProperty, $this->getUnassignedVCardProperty()) == 0) {
			$properties[] = array('unassigned' => true);
		} else {
			foreach ($this->config_content->ldap_entries->ldif_entry as $ldif_entry) {
				if ($lProperty == $ldif_entry['name']) {
					// $ldif_entry['name'] is the right config xml
					foreach ($ldif_entry->vcard_entry as $vcard_entry) {
						$type=isset($vcard_entry['type'])?$vcard_entry['type']:"";
						$position=isset($vcard_entry['position'])?$vcard_entry['position']:"";
						$image=isset($ldif_entry['image'])?$ldif_entry['image']:"";
						$properties[] = array('property' => $vcard_entry['property'], 'type' => $type, 'position' => $position, 'image' => $image);
					}
				}
			}
		}
		return $properties;
	}
	
	/**
	 * @brief return the ldif entries corresponding to the name and type given in parameter
	 * @param $propertyName the name of the vcard parameter
	 * @param $propertyType the type of the parameter
	 */
	public function getLdifEntry($propertyName, $propertyType) {
		//\OC_Log::write('ldapconnector', __METHOD__."looking for $propertyName, $propertyType", \OC_Log::DEBUG);
		if ($this->config_content !=null) {
			$ldifEntries = array();
			foreach ($this->config_content->vcard_entries->vcard_entry as $vcardEntry) {
				if (strcasecmp($vcardEntry['property'], $propertyName) == 0 && (!isset($vcardEntry['type']) || strcasecmp($vcardEntry['type'], $propertyType) == 0) && strcasecmp($vcardEntry['enabled'], 'true') == 0) {
					foreach($vcardEntry->ldif_entry as $ldifEntry) {
						$params = array();
						$params['name'] = $ldifEntry['name'];
						if (isset($ldifEntry['vcard_position'])) {
							$params['vcard_position'] = intval($ldifEntry['vcard_position']);
						}
						if (isset($vcardEntry['image']) && strcasecmp($vcardEntry['image'], 'true') == 0) {
							$params['image'] = true;
						} else {
							$params['image'] = false;
						}
						$ldifEntries[] = $params;
					}
				}
			}
			if (count($ldifEntries) == 0) {
				$ldifEntries[] = array('unassigned' => true);
			}
			return $ldifEntries;
		}
		return null;
	}
	
	/**
	 * @brief transform a vcard into a ldap entry
	 * @param VCard $vcard
	 * @return array|false
	 */
	public function VCardToLdap($vcard) {

		$ldifReturn = array(); // Array to return
		foreach ($vcard->children() as $property) {
			// Basically, a $property can be converted into one or multiple ldif entries
			// and also some vcard properties can have data that can be split to fill different ldif entries
			$ldifArray = self::getLdifProperty($property);
			
			if (count($ldifArray) > 0) {
				self::updateLdifProperty($ldifReturn, $ldifArray);
			}
		}
		self::validateLdapEntry($ldifReturn);
		return $ldifReturn;
	}
	
	/**
	 * @brief transform finds the ldif entries associated with the property
	 * @param Property $property
	 * @return array|false
	 */
	public function getLdifProperty($property) {
		$ldifReturn = array();
		// Only one value per property, so we loop into types
		// then for each one, look into config xml if there are ldif entries matching
		$ldifEntries = self::getLdifEntry($property->name, $property['TYPE']);
		// If one is found, create a tab entry like tab['ldif_entry']
		if ($ldifEntries != null && count($ldifEntries)>0) {
			foreach ($ldifEntries as $ldifEntry) {
				if (isset($ldifEntry['unassigned'])) {
					if ((strcasecmp($property->name, "REV") != 0) && (strcasecmp($property->name, "VERSION") != 0) && (strcasecmp($property->name, "X-LDAP-DN") != 0)) {
						// The unassigned properties are set in the ldap unassignedVCardProperty
						$ldifReturn[(string)$this->getUnassignedVCardProperty()] = array($property->serialize());
					}
				} else {
					// Last, if the ldif entry has a vcard_position set, take only the value in the position index
					$value = $property->value;
					if (isset($ldifEntry['vcard_position'])) {
						//\OC_Log::write('ldapconnector', __METHOD__." position set ".$ldifEntry['vcard_position'], \OC_Log::DEBUG);
						$tmpValues = explode(";", $property->value);
						$value = $tmpValues[$ldifEntry['vcard_position']];
					}
					//\OC_Log::write('ldapconnector', __METHOD__.__METHOD__." entry : ".$ldifEntry['name']." - value : $value", \OC_Log::DEBUG);
					// finally, sets tab['ldif_entry'][] with the value
					if (strcmp($value, "") != 0) {
						if ($ldifEntry['image']) {
							$ldifReturn[(string)$ldifEntry['name']] = array(base64_decode($value));
						} else {
							$ldifReturn[(string)$ldifEntry['name']] = array($value);
						}
					}
				}
			}
		}
		return $ldifReturn;
	}
	
	/**
	 * @brief updates the ldifEntry with $ldifNewValues
	 * @param $ldifEntry the array to modify
	 * @param $ldifNewValues the new values
	 * @return boolean
	 */
	public function updateLdifProperty(&$ldifEntries, $ldifNewValues) {
		foreach ($ldifNewValues as $key => $value) {
			if (isset($ldifEntries[$key])) {
				$ldifEntries[$key] = array_merge($ldifEntries[$key], $value);
			} else {
				$ldifEntries[$key] = $value;
			}
		}
	}
	
	/**
	 * @brief returns all the ldap entries managed
	 * @return array
	 */
	public function getLdapEntries() {
		if ($this->config_content != null) {
			$to_return = array('modifytimestamp');
			
			$unassigned = $this->getUnassignedVCardProperty();
			if ($unassigned) {
				$to_return[] = $unassigned;
			}
			
			foreach ($this->config_content->ldap_entries[0]->ldif_entry as $ldif_entry) {
				$to_return[] = $ldif_entry['name'];
			}
			return $to_return;
		} else {
			return null;
		}
	}
	
	/**
	 * @brief returns the ldif entry for the VCard properties that don't have a ldap correspondance
	 * @return string|false
	 */
	public function getUnassignedVCardProperty() {
		if ($this->config_content != null && $this->config_content->ldap_entries[0]->ldap_core[0]->unassigned_vcard_property['ldap_name'] != null) {
			return ($this->config_content->ldap_entries[0]->ldap_core[0]->unassigned_vcard_property['ldap_name']);
		} else {
			return false;
		}
	}
	
	/**
	 * @brief get the id attribute in the ldap
	 * @return string|false
	 */
	public function getLdapId() {
		if ($this->config_content != null && $this->config_content->ldap_entries[0]->ldap_core[0]->ldap_id['name'] != null) {
			return ($this->config_content->ldap_entries[0]->ldap_core[0]->ldap_id['name']);
		} else {
			return false;
		}
	}
	
	/**
	 * @brief get the xml config name
	 * @return string|false
	 */
	public function getXmlConfigName() {
		if ($this->config_content != null && $this->config_content['name'] != null) {
			return ($this->config_content['name']);
		} else {
			return false;
		}
	}
	
	/**
	 * @brief checks if the ldapEntry is valid and fixes if possible
	 * @param $ldapEntry array
	 * @return boolean
	 */
	public function validateLdapEntry(&$ldapEntry) {
		if ($this->config_content != null) {
			$ldapEntry['objectclass'] = array();
			
			// Fill $ldapEntry with the objectClass params
			foreach ($this->config_content->ldap_entries[0]->ldap_core[0]->object_class as $object_class) {
				$ldapEntry['objectclass'][] = (string)$object_class['name'];
			}
			
			foreach ($this->config_content->ldap_entries[0]->ldap_core[0]->not_null as $not_null) {
				$key = (string)$not_null['name'];
				
				// Repair $ldapEntry if a field is null or empty
				if (!array_key_exists($key, $ldapEntry) || strcmp('', $ldapEntry[$key][0]) == 0) {
					
					// Switch with another entry
					if (isset($not_null->action_switch[0])) {
						$ldapEntry[$key][0] = $ldapEntry[(string)$not_null->action_switch[0]['name']][0];
						unset($ldapEntry[(string)$not_null->action_switch[0]['name']][0]);
						if (count($ldapEntry[(string)$not_null->action_switch[0]['name']]) == 0) {
							unset($ldapEntry[(string)$not_null->action_switch[0]['name']]);
						}
					} else if (isset($not_null->action_default[0])) {
						// Fill with a default value
						$ldapEntry[$key][0] = (string)$not_null->action_default[0]['value'];
					}
				}
			}
			
			foreach ($this->config_content->vcard_entries[0]->vcard_entry as $vcardEntry)  {
				foreach ($vcardEntry->ldif_entry as $ldifEntry) {
					// Remove duplicates if relevant
					if (strcmp("true", $ldifEntry['unique'])==0 && array_key_exists((string)$ldifEntry['name'], $ldapEntry)) { // Y aller à coup de "bool array_key_exists ( mixed $key , array $search )"
						// Holy hand-grenade, there are like 3 imbricated loops...
						$ldapEntry[(string)$ldifEntry['name']] = array_unique($ldapEntry[(string)$ldifEntry['name']]);
					}
				}
			}
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @brief adds empty entries in $dest if $dest doesn't have those entries and if $source has
	 * otherwise, I couldn't find how to remove attributes
	 * @param $source the source ldap entry as model
	 * @param $dest the destination entry to add empty params if we have to
	 */
	public function insertEmptyEntries($source, &$dest) {
		for ($i=0; $i<$source["count"]; $i++) {
			
			$lProperty = $source[$i];
			if (!isset($dest[$lProperty]) && $lProperty != 'modifytimestamp') {
				$dest[$lProperty] = array();
			}
		}
	}
}

?>
