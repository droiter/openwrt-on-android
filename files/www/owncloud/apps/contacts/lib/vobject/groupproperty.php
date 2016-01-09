<?php
/**
 * ownCloud - VObject Group Property
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

namespace OCA\Contacts\VObject;

use OC\VObject\CompoundProperty;

/**
 * This class adds convenience methods for the CATEGORIES property.
 *
 * NOTE: Group names are case-insensitive.
 */
class GroupProperty extends CompoundProperty {

	/**
	* Add a group.
	*
	* NOTE: We cannot just use add() as that method name is used in
	* \Sabre\VObject\Property
	*
	* @param string $name
	* @return bool
	*/
	public function addGroup($name) {
		$name = trim($name);
		if($this->hasGroup($name)) {
			return false;
		}
		$groups = $this->getParts();
		// Remove empty elements
		$groups = array_filter($groups, 'strlen');
		$groups[] = $name;
		$this->setParts($groups);
		return true;
	}

	/**
	* Remove an existing group.
	*
	* @param string $name
	* @return bool
	*/
	public function removeGroup($name) {
		$name = trim($name);
		if(!$this->hasGroup($name)) {
			return false;
		}
		$groups = $this->getParts();
		$groups = array_map('trim', $groups);
		array_splice($groups, $this->array_searchi($name, $groups), 1);
		$this->setParts($groups);
		return true;
	}

	/**
	* Test it a group by that name exists.
	*
	* @param string $name
	* @return bool
	*/
	public function hasGroup($name) {
		$name = trim($name);
		$groups = $this->getParts();
		$groups = array_map('trim', $groups);
		return $this->in_arrayi($name, $groups);
	}

	/**
	* Rename an existing group.
	*
	* @param string $from
	* @param string $to
	*/
	public function renameGroup($from, $to) {
		$from = trim($from);
		$to = trim($to);
		if(!$this->hasGroup($from)) {
			return;
		}
		$groups = $this->getParts();
		$groups = array_map('trim', $groups);
		$groups[$this->array_searchi($from, $groups)] = $to;
		$this->setParts($groups);
	}

	// case-insensitive in_array
	protected function in_arrayi($needle, $haystack) {
		if(!is_array($haystack)) {
			return false;
		}
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}

	// case-insensitive array_search
	protected function array_searchi($needle, $haystack) {
		if(!is_array($haystack)) {
			return false;
		}
		return array_search(strtolower($needle), array_map('strtolower', $haystack));
	}
}