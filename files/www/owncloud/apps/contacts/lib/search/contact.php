<?php

/**
 * ownCloud
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

namespace OCA\Contacts\Search;

/**
 * A contact search result
 */
class Contact extends \OCP\Search\Result {

	/**
	 * Type name; translated in templates
	 *
	 * @var string
	 */
	public $type = 'contact';

	/**
	 * Contact address
	 *
	 * @var string
	 */
	public $address;

	/**
	 * Contact phone numbers
	 *
	 * @var string
	 */
	public $phone;

	/**
	 * Contact e-mail
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Contact nickname
	 *
	 * @var string
	 */
	public $nickname;

	/**
	 * Contact organization
	 *
	 * @var string
	 */
	public $organization;

	/**
	 * Constructor
	 *
	 * @param array $data
	 * @return \OCA\Contacts\Search\Contact
	 */
	public function __construct(array $data = null) {
		$this->id = $data['id'];
		$this->name = stripcslashes($data['FN']);
		$this->link = \OCP\Util::linkToRoute('contacts_index') . '#' . $data['id'];
		$this->address = $this->checkAndMerge($data, 'ADR');
		$this->phone = $this->checkAndMerge($data, 'TEL');
		$this->email = $this->checkAndMerge($data, 'EMAIL');
		$this->nickname = $this->checkAndMerge($data, 'NICKNAME');
		$this->organization = $this->checkAndMerge($data, 'ORG');
	}

	/**
	 * Check a contact property and return its value; handles properties with
	 * multiple values by merging them into a comma-separated list
	 *
	 * @param array $data
	 * @param string $property
	 * @return string or null
	 */
	private function checkAndMerge($data, $property) {
		// check property
		if (!is_array($data) || !array_key_exists($property, $data)) {
			return null;
		}
		// check value
		if (!is_array($data[$property])) {
			return stripcslashes($data[$property]);
		}
		// or merge array
		if (count($data[$property]) > 0) {
			$list = array();
			foreach ($data[$property] as $value) {
				$list[] = stripcslashes($value);
			}
			return implode(', ', $list);
		}
		// default
		return null;
	}

}