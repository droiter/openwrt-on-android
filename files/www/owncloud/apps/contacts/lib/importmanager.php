<?php
/**
 * ownCloud - Import manager
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
 
namespace OCA\Contacts;
use Sabre\VObject\Component;
use OCA\Contacts\Connector\ImportCsvConnector;
use OCA\Contacts\Connector\ImportVCardConnector;
use OCA\Contacts\Connector\ImportLdifConnector;
use OCA\Contacts\Addressbook;

/**
 * Manages the import with basic functionalities
 */
class ImportManager {
	
	private function loadXmlFile($path) {
		if (file_exists($path)) {
			$format = simplexml_load_file ( $path );
			if ($format) {
				if (isset($format->import_core)
				&& isset($format->import_core->name)
				&& isset($format->import_core->display_name)
				&& isset($format->import_core->type)
				&& isset($format->import_core->active)
				&& $format->import_core->active == '1') {
					return $format;
				}
			}
		}
		return false;
	}
	
	/**
	 * @brief return the different import formats available by scanning the contacts/formats folder
	 * @return array(string, string)
	 */
	public function getTypes() {
		$prefix = "import_";
		$suffix = "_connector.xml";
		$path = __DIR__ . "/../formats/";
		$files = scandir($path);
		$formats = array();
		foreach ($files as $file) {
			if (!strncmp($file, $prefix, strlen($prefix)) && substr($file, - strlen($suffix)) === $suffix) {
				$format = $this->loadXmlFile(realpath($path.$file));
				if ($format) {
					$formats[(string)$format->import_core->name] = (string)$format->import_core->display_name;
				}
			}
		}
		return $formats;
	}
	
	/**
	 * @brief get all the preferences for the addressbook
	 * @param string $id
	 * @return SimpleXml
	 */
	public function getType($typeName) {
		$path = __DIR__ . "/../formats/import_" . $typeName . "_connector.xml";
		return $this->loadXmlFile($path);
	}
		
	/**
	 * @brief imports the file with the selected type, and converts into VCards
	 * @param $file the path to the file
	 * @param $typeName the type name to use as stored into the app settings
	 * @param $limit the number of elements to import
	 * @return an array containing VCard elements|false if empty of error
	 */
	public function importFile($file, $typeName, $limit=-1) {
		\OCP\Util::writeLog('contacts import manager', __METHOD__.' importing as '.$typeName, \OCP\Util::INFO);
		$connector = $this->getConnector($typeName);
		if ($connector) {
			$elements = $connector->getElementsFromInput($file, $limit);
			if (count($elements) > 0) {
				return $elements;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function getConnector($type) {
		$importType = $this->getType($type);
		$elements = array();
		if (!$importType) {
			return false;
		}
		if ((string)$importType->import_core->type == 'csv') {
			// use class ImportCsvConnector
			return new ImportCsvConnector($importType);
		} else if ((string)$importType->import_core->type == 'vcard') {
			// use class importVcardConnector
			return new ImportVCardConnector($importType);
		} else if ((string)$importType->import_core->type == 'ldif') {
			// use class importVcardConnector
			return new ImportLdifConnector($importType);
		}
		return false;
	}
	
	/**
	 * @brief import the first element of the file with all the types
	 * detects wich imported type has the least elements "X-Unknown-Element"
	 * then returns the corresponding type
	 * @param $file the path to the file
	 * @return array containing the probability for each format
	 */
	public function detectFileType($file) {
		$types = $this->getTypes();
		$probability = array();
		foreach ($types as $type => $description) {
			$connector = $this->getConnector($type);
				if ($connector) {
					$probability[$type] = $connector->getFormatMatch($file);
				}
		}
		return $probability;
	}
	
	/**
	 * @brief get the raw entries from the input file
	 * @param $file the path to the file
	 * @param $limit the maximum number of entries to return (-1: no limit)
	 * @return array|false
	 */
	public function getEntries($file, $limit=-1) {
		return $connector->getElementsFromInput($file, $limit);
	}
}

?>
