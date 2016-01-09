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

use Sabre\VObject\Component;
use \SplFileObject as SplFileObject;
use Sabre\VObject\StringUtil;

/**
 * @brief Implementation of the Import class for CSV format
 * Doesn't really like csv that has fields with new lines in it, beware !
 */
class ImportCsvConnector extends ImportConnector {

	/**
	 * @brief separates elements from the input stream according to the entry_separator value in config
	 * ignoring the first line if mentionned in the config
	 * @param $file the input file to import
	 * @param $limit the number of elements to return (-1 = no limit)
	 * @return array(array(data), array(titles))
	 */
	public function getElementsFromInput($file, $limit=-1) {

		$linesAndTitles = $this->getSourceElementsFromFile($file, $limit);
		$lines = $linesAndTitles[0];
		$titles = $linesAndTitles[1];
		$elements = array();
		foreach ($lines as $line) {
			$elements[] = $this->convertElementToVCard($line, $titles);
		}

		return array_values($elements);
	}

	/**
	 * @brief parses the file in csv format
	 * @param $file the input file to import
	 * @param $limit the number of elements to return (-1 = no limit)
	 * @return array(array(data), array(titles))
	 */
	private function getSourceElementsFromFile($file, $limit=-1) {
		if (file_put_contents($file, StringUtil::convertToUTF8(file_get_contents($file)))) {
			$csv = new SplFileObject($file, 'r');
			$csv->setFlags(SplFileObject::READ_CSV);

			$delimiter = '';
			if (isset($this->configContent->import_core->delimiter)) {
				$delimiter = (string)$this->configContent->import_core->delimiter;
			} else {
				// Look for the delimiter in the first line, should be the most present character between ',', ';' and '\t'
				$splFile = new SplFileObject($file);
				$firstLine = $splFile->fgets();
				$nbComma = substr_count($firstLine, ',');
				$nbSemicolon = substr_count($firstLine, ';');
				$nbTab = substr_count($firstLine, "\t");
				if ($nbComma > $nbSemicolon && $nbComma > $nbTab) {
					// Comma it is
					$delimiter = ',';
				} else if ($nbSemicolon > $nbComma && $nbSemicolon > $nbTab) {
					// Semicolon it is
					$delimiter = ';';
				} else if ($nbTab > $nbComma && $nbTab > $nbSemicolon) {
					// Tab it is
					$delimiter = "\t";
				} else if ($nbTab == 0 && $nbComma == 0 && $nbSemicolon == 0) {
					// We have a problem, no delimiter found
					return array(array(), array());
				}
			}
			$csv->setCsvControl($delimiter, "\"", "\\");

			$ignoreFirstLine = (isset($this->configContent->import_core->ignore_first_line)
									&& (((string)$this->configContent->import_core->ignore_first_line) == 'true')
										|| ((string)$this->configContent->import_core->ignore_first_line) == '1');

			$titles = false;

			$lines = array();

			$index = 0;
			foreach($csv as $line) {
				if (!($ignoreFirstLine && $index == 0) && count($line) > 1) { // Ignore first line

					$lines[] = $line;

					if (count($lines) == $limit) {
						break;
					}
				} else if ($ignoreFirstLine && $index == 0) {
					$titles = $line;
				}
				$index++;
			}

			return array($lines, $titles);
		} else {
			error_log("Error converting file to utf8");
			return array(array(), array());
		}
	}

	/**
	 * @brief converts a unique element into a owncloud VCard
	 * @param $element the element to convert
	 * @return VCard, all unconverted elements are stored in X-Unknown-Element parameters
	 */
	public function convertElementToVCard($element, $title = null) {
		$vcard = \Sabre\VObject\Component::create('VCARD');

		$nbElt = count($element);
		for ($i=0; $i < $nbElt; $i++) {
			if ($element[$i] != '') {
				//$importEntry = false;
				// Look for the right import_entry
				if (isset($this->configContent->import_core->base_parsing)) {
					if (strcasecmp((string)$this->configContent->import_core->base_parsing, 'position') == 0) {
						$importEntry = $this->getImportEntryFromPosition((String)$i);
					} else if (strcasecmp((string)$this->configContent->import_core->base_parsing, 'name') == 0 && isset($title[$i])) {
						$importEntry = $this->getImportEntryFromName($title[$i]);
					}
				}
				if ($importEntry) {
					// Create a new property and attach it to the vcard
					$value = $element[$i];
					if (isset($importEntry['remove'])) {
						$value = str_replace($importEntry['remove'], '', $element[$i]);
					}
					$values = array($value);
					if (isset($importEntry['separator'])) {
						$values = explode($importEntry['separator'], $value);
					}
					
					foreach ($values as $oneValue) {
						if (isset($importEntry->vcard_favourites)) {
							foreach ($importEntry->vcard_favourites as $vcardFavourite) {
								if (strcasecmp((string)$vcardFavourite, trim($oneValue)) == 0) {
									$property = \Sabre\VObject\Property::create("X-FAVOURITES", 'yes');
									$vcard->add($property);
								} else {
									$property = $this->getOrCreateVCardProperty($vcard, $importEntry->vcard_entry);
									$this->updateProperty($property, $importEntry, trim($oneValue));
								}
							}
						} else {
							$property = $this->getOrCreateVCardProperty($vcard, $importEntry->vcard_entry);
							$this->updateProperty($property, $importEntry, trim($oneValue));
						}
					}
				} else if (isset($element[$i]) && isset($title[$i])) {
					$property = \Sabre\VObject\Property::create("X-Unknown-Element", StringUtil::convertToUTF8($element[$i]));
					$property->parameters[] = new \Sabre\VObject\Parameter('TYPE', ''.StringUtil::convertToUTF8($title[$i]));
					$vcard->add($property);
				}
			}
		}
		$vcard->validate(\Sabre\VObject\Component\VCard::REPAIR);
		return $vcard;
	}

	/**
	 * @brief gets the import entry corresponding to the position given in parameter
	 * @param $position the position to look for in the connector
	 * @return int|false
	 */
	private function getImportEntryFromPosition($position) {
		$nbElt = $this->configContent->import_entry->count();
		for ($i=0; $i < $nbElt; $i++) {
			if ($this->configContent->import_entry[$i]['position'] == $position && $this->configContent->import_entry[$i]['enabled'] == 'true') {
				return $this->configContent->import_entry[$i];
			}
		}
		return false;
	}

	/**
	 * @brief gets the import entry corresponding to the name given in parameter
	 * @param $name the parameter name to look for in the connector
	 * @return string|false
	 */
	private function getImportEntryFromName($name) {
		$nbElt = $this->configContent->import_entry->count();
		for ($i=0; $i < $nbElt; $i++) {
			if ($this->configContent->import_entry[$i]['name'] == StringUtil::convertToUTF8($name) && $this->configContent->import_entry[$i]['enabled'] == 'true') {
				return $this->configContent->import_entry[$i];
			}
			if (isset($this->configContent->import_entry[$i]->altname)) {
				foreach ($this->configContent->import_entry[$i]->altname as $altname) {
					if ($altname == StringUtil::convertToUTF8($name) && $this->configContent->import_entry[$i]['enabled'] == 'true') {
						return $this->configContent->import_entry[$i];
					}
				}
			}
		}
		return false;
	}

	/**
	 * @brief returns the probability that the first element is a match for this format
	 * @param $file the file to examine
	 * @return 0 if not a valid csv file
	 *         1 - 0.5*(number of untranslated elements/total number of elements)
	 * The more the first element has untranslated elements, the more the result is close to 0.5
	 */
	public function getFormatMatch($file) {
		// Examining the first element only
		$partsAndTitle = $this->getSourceElementsFromFile($file, 1);
		$parts = $partsAndTitle[0];
		$titles = $partsAndTitle[1];

		if (!$parts || ($parts && isset($this->configContent->import_core->expected_columns)
			&& count($parts[0]) != (string)$this->configContent->import_core->expected_columns)
		) {
			// Doesn't look like a csv file
			return 0;
		} else {
			$element = $this->convertElementToVCard($parts[0], $titles);
			$unknownElements = $element->select("X-Unknown-Element");
			return (1 - (0.5 * count($unknownElements)/count($parts[0])));
		}
	}
}

?>
