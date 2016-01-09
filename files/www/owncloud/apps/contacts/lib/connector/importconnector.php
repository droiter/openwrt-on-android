<?php
/**
 * ownCloud - CSV Import connector
 *
 * @author Nicolas Mora
 * @copyright 2013-2014 Nicolas Mora mail@babelouest.org
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

use Sabre\VObject\Component,
	Sabre\VObject\StringUtil;

/**
 * Abstract class used to implement import classes
 */
abstract class ImportConnector {

	// XML Configuration, class SimpleXml format
	protected $configContent;
	
	public function __construct($xml_config = null) {
		if ($xml_config != null) {
			$this->setConfig($xml_config);
		}
	}
	
	// returns a table containing converted elements from the input file
	abstract function getElementsFromInput($input, $limit=-1);
	
	// returns a single converted element
	abstract function convertElementToVCard($element);
	
	// returns the probability that the file matchs the current format
	abstract function getFormatMatch($file);
	
	public function setConfig($xml_config) {
		$this->configContent = $xml_config;
	}
	
	/**
	 * @brief updates a property given in parameter with the value and using the importEntry to set the different parameters
	 * @param $property the property to update
	 * @param $importEntry the entry configuration to update in SimpleXml format
	 * @value the value to update
	 */
	protected function updateProperty(&$property, $importEntry, $value) {
		if (isset($property) && isset($importEntry) && isset($value)) {
			if (isset($importEntry->vcard_entry)) {
				if (isset($importEntry->vcard_entry['type'])) {
					$property->parameters[] = new \Sabre\VObject\Parameter('TYPE', ''.StringUtil::convertToUTF8($importEntry->vcard_entry['type']));
				}
				if (isset($importEntry->vcard_entry->additional_property)) {
					foreach ($importEntry->vcard_entry->additional_property as $additionalProperty) {
						$property->parameters[] = new \Sabre\VObject\Parameter(''.$additionalProperty['name'], ''.$additionalProperty['value']);
					}
				}
				if (isset($importEntry->vcard_entry['prefix'])) {
					$value = $importEntry->vcard_entry['prefix'].$value;
				}
				if (isset($importEntry->vcard_entry['group'])) {
					$property->group = $importEntry->vcard_entry['group'];
				}
				if (isset($importEntry->vcard_entry['position'])) {
					$separator=";";
					if (isset($importEntry->vcard_entry['separator'])) {
						$separator=$importEntry->vcard_entry['separator'];
					}
					$position = $importEntry->vcard_entry['position'];
					$vArray = explode($separator, $property);
					$vArray[intval($position)] = StringUtil::convertToUTF8($value);
					$property->setValue(implode($separator, $vArray));
				} else {
					if (isset($importEntry->vcard_entry['value'])) {
						$property->parameters[] = new \Sabre\VObject\Parameter('TYPE', ''.StringUtil::convertToUTF8($value));
					} else {
						$curVal = $property->getValue();
						if ($curVal != '') {
							$curVal .= ',' . StringUtil::convertToUTF8($value);
						} else {
							$curVal = StringUtil::convertToUTF8($value);
						}
						$property->setValue($curVal);
					}
				}
			}
			if (isset($importEntry->vcard_parameter)) {
				$property->parameters[] = new \Sabre\VObject\Parameter($importEntry->vcard_parameter['parameter'], ''.StringUtil::convertToUTF8($value));
			}
		}
	}
		
	/**
	 * @brief modifies a vcard property array with the image
	 */
	public function updateImageProperty(&$property, $entry, $version=null) {
		$image = new \OC_Image();
		$image->loadFromData($entry);
		if (strcmp($version, '4.0') == 0) {
			$type = $image->mimeType();
		} else {
			$arrayType = explode('/', $image->mimeType());
			$type = strtoupper(array_pop($arrayType));
		}
		$property->add('ENCODING', 'b');
		$property->add('TYPE', $type);
		$property->setValue($image->__toString());
	}

	/**
	 * @brief returns the vcard property corresponding to the parameter
	 * creates the property if it doesn't exists yet
	 * @param $vcard the vcard to get or create the properties with
	 * @param $importEntry the parameter to find
	 * @return the property|false
	 */
	protected function getOrCreateVCardProperty(&$vcard, $importEntry) {
		
		if (isset($vcard) && isset($importEntry)) {
			// looking for a property with the same name
			$properties = $vcard->select($importEntry['property']);
			foreach ($properties as $property) {
				if ($importEntry['type'] == null && !isset($importEntry->additional_property)) {
					return $property;
				}
				foreach ($property->parameters as $parameter) {
					// Filtering types
					if ($parameter->name == 'TYPE' && !strcmp($parameter->value, $importEntry['type'])) {
						$found=0;
						if (isset($importEntry->additional_property)) {
							// Filtering additional properties if necessary (I know, there are a lot of inner loops, sorry)
							foreach($importEntry->additional_property as $additional_property) {
								if ((string)$parameter->name == $additional_property['name']) {
									$found++;
								}
							}
							if ($found == count($importEntry->additional_property)) {
								return $property;
							}
						}
						return $property;
					}
				}
				
				if (isset($importEntry['group']) && $property->group == $importEntry['group']) {
					return $property;
				}
			}		
			
			// Property not found, creating one
			$property = \Sabre\VObject\Property::create($importEntry['property']);
			$vcard->add($property);
			if ($importEntry['type']!=null) {
				$property->parameters[] = new \Sabre\VObject\Parameter('TYPE', ''.StringUtil::convertToUTF8($importEntry['type']));
				switch ($importEntry['property']) {
					case "ADR":
						$property->setValue(";;;;;;");
						break;
					case "FN":
						$property->setValue(";;;;");
						break;
				}
			}
			if ($importEntry['group']!=null) {
				$property->group = $importEntry['group'];
			}
			return $property;
		} else {
			return false;
		}
	}
}

?>
