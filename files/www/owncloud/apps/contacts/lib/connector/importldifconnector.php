<?php
/**
 * ownCloud - LDIF Import connector
 *
 * @author Nicolas Mora
 * @copyright 2014 Nicolas Mora mail@babelouest.org
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
	Sabre\VObject\StringUtil,
	Sabre\VObject;

/**
 * @brief Implementation of the LDIF import format
 */
class ImportLdifConnector extends ImportConnector{

	/**
	 * @brief separates elements from the input stream according to the entry_separator value in config
	 * ignoring the first line if mentionned in the config
	 * @param $input the input file to import
	 * @param $limit the number of elements to return (-1 = no limit)
	 * @return array of strings
	 */
	public function getElementsFromInput($file, $limit=-1) {

		$parts = $this->getSourceElementsFromFile($file, $limit);
		
		$elements = array();
		foreach($parts as $part)
		{
			$elements[] = $this->convertElementToVCard($part);
		}
		
		return array_values($elements);
	}
	
	/**
	 * @brief parses the file in vcard format
	 * @param $file the input file to import
	 * @param $limit the number of elements to return (-1 = no limit)
	 * @return array()
	 */
	private function getSourceElementsFromFile($file, $limit=-1) {
		$file = StringUtil::convertToUTF8(file_get_contents($file));

		$nl = "\n";
		$replaceFrom = array("\r","\n ");
		$replaceTo = array("\n","");
		foreach ($this->configContent->import_core->replace as $replace) {
			if (isset($replace['from']) && isset($replace['to'])) {
				$replaceFrom[] = $replace['from'];
				$replaceTo[] = $replace['to'];
			}
		}
		
		$file = str_replace($replaceFrom, $replaceTo, $file);
		
		$lines = explode($nl, $file);
		$parts = array();
		$card = array();
		$numParts = 0;
		foreach($lines as $line) {
			if (!preg_match("/^#/", $line)) { // Ignore comment line
				if(preg_match("/^\w+:: /",$line)) {
					$kv = explode(':: ', $line, 2);
					$key = $kv[0];
					$value = base64_decode($kv[1], true);
				} else {
					$kv = explode(': ', $line, 2);
					$key = $kv[0];
					if(count($kv) == 2) {
						$value = $kv[1];
					} else {
						$value = "";
					}
				}
				if ($key == "dn") {
					if (count($card) > 0) {
						$numParts++;
						if ($limit > -1 && count($card) == $limit) {
							break;
						}
						$parts[] = $card;
					}
					$card = array(array($key, $value));
				} else if ($key != "" && $key != "version" && $value != "") {
					$card[] = array($key, $value);
				}
			}
		}
		if ($numParts <= $limit && count($card) > 0) {
			$parts[] = $card;
		}
		return $parts;
	}
	
	/**
	 * @brief converts a ldif into a owncloud VCard
	 * @param $element the VCard element to convert
	 * @return VCard
	 */
	public function convertElementToVCard($element) {
		$dest = \Sabre\VObject\Component::create('VCARD');
		
		foreach ($element as $ldifProperty) {
			$importEntry = $this->getImportEntry($ldifProperty[0]);
			if ($importEntry) {
				$value = $ldifProperty[1];
				if (isset($importEntry['remove'])) {
					$value = str_replace($importEntry['remove'], '', $ldifProperty[1]);
				}
				$values = array($value);
				if (isset($importEntry['separator'])) {
					$values = explode($importEntry['separator'], $value);
				}
				
				foreach ($values as $oneValue) {
					$this->convertElementToProperty($oneValue, $importEntry, $dest);
				}
			} else {
				$property = \Sabre\VObject\Property::create("X-Unknown-Element", ''.StringUtil::convertToUTF8($ldifProperty[1]));
				$property->parameters[] = new \Sabre\VObject\Parameter('TYPE', ''.StringUtil::convertToUTF8($ldifProperty[0]));
				$dest->add($property);
			}
		}
		
		$dest->validate(\Sabre\VObject\Component\VCard::REPAIR);
		return $dest;
	}
	
	/**
	 * @brief converts an LDIF element into a VCard property
	 * and updates the VCard
	 * @param $value the LDIF value
	 * @param $importEntry the VCard entry to modify
	 * @param $dest the VCard to modify (for adding a X-FAVOURITE property)
	 */
	private function convertElementToProperty($value, $importEntry, &$dest) {
		if (isset($importEntry->vcard_favourites)) {
			foreach ($importEntry->vcard_favourites as $vcardFavourite) {
				if (strcasecmp((string)$vcardFavourite, trim($value)) == 0) {
					$property = \Sabre\VObject\Property::create("X-FAVOURITES", 'yes');
					$dest->add($property);
				} else {
					$property = $this->getOrCreateVCardProperty($dest, $importEntry->vcard_entry);
					if (isset($importEntry['image']) && $importEntry['image'] == "true") {
						$this->updateImageProperty($property, $value);
					} else {
						$this->updateProperty($property, $importEntry, $value);
					}
				}
			}
		} else {
			$property = $this->getOrCreateVCardProperty($dest, $importEntry->vcard_entry);
			if (isset($importEntry['image']) && $importEntry['image'] == "true") {
				$this->updateImageProperty($property, $value);
			} else {
				$this->updateProperty($property, $importEntry, $value);
			}
		}
	}
	
	/**
	 * @brief tests if the property has to be translated by looking for its signature in the xml configuration
	 * @param $property Sabre VObject Property too look
	 * @param $vcard the parent Sabre VCard object to look for a 
	 */
	private function getImportEntry($property) {
		foreach ($this->configContent->import_entry as $importEntry) {
			if ($importEntry['name'] == $property) {
				return $importEntry;
			}
		}
		return false;
	}
	
	/**
	 * @brief returns the probability that the first element is a match for this format
	 * @param $file the file to examine
	 * @return 0 if not a valid ldif file
	 *         1 - 0.5*(number of untranslated elements/total number of elements)
	 * The more the first element has untranslated elements, the more the result is close to 0.5
	 */
	public function getFormatMatch($file) {
		// Examining the first element only
		$parts = $this->getSourceElementsFromFile($file, 1);

		if (!$parts) {
			// Doesn't look like a ldif file
			return 0;
		} else {
			$element = $this->convertElementToVCard($parts[0]);
			$unknownElements = $element->select("X-Unknown-Element");
			return (1 - (0.5 * count($unknownElements)/count($parts[0])));
		}
	}	
}

?>
