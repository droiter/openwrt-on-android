<?php
/**
 * Copyright (c) 2013 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts\Backend;

class Mock extends AbstractBackend {

	public $name = 'mock';
	public $addressBooks;
	public $contacts;
	public $userid;

	function __construct($userid = null, $addressBooks = null, $contacts = null) {

		$this->userid = $userid ? $userid : \OCP\User::getUser();
		$this->addressBooks = $addressBooks;
		$this->contacts = $contacts;

		if (is_null($this->addressBooks)) {
			$this->addressBooks = array(
				array(
					'id' => 'foo',
					'owner' => $userid,
					'displayname' => 'd-name',
					'permissions' => \OCP\PERMISSION_ALL,
				),
			);

			$this->contacts = array(
				'foo' => array(
					'123' =>
					array(
						'id' => '123',
						'displayname' => 'Max Mustermann',
						'carddata' => file_get_contents(__DIR__ . '/../../data/test1.vcf')
					)
				),
			);
		}

	}


	function getAddressBooksForUser(array $options = array()) {

		$books = array();
		foreach($this->addressBooks as $book) {
			if ($book['owner'] === $this->userid) {
				$books[] = $book;
			}
		}
		return $books;

	}

	function getAddressBook($addressBookId, array $options = array()) {

		foreach($this->addressBooks as &$book) {
			if ($book['id'] === $addressBookId) {
				return $book;
			}
		}

	}

	function updateAddressBook($addressBookId, array $changes, array $options = array()) {

		if(count($changes) === 0 || !isset($changes['displayname'])) {
			return false;
		}

		foreach($this->addressBooks as &$book) {
			if ($book['id'] === $addressBookId) {
				foreach($changes as $key => $value) {
					$book[$key] = $value;
					return true;
				}
			}
		}

		return false;

	}

	function createAddressBook(array $properties, array $options = array()) {

		if(count($properties) === 0 || !isset($properties['displayname'])) {
			return false;
		}

		$id = \OC_Util::generateRandomBytes('4');
		$this->addressBooks[] = array_merge($properties, array(
			'id' => $id,
			'permissions' => \OCP\PERMISSION_ALL,
			'owner' => $this->userid,
		));

		return $id;
	}

	function deleteAddressBook($addressBookId, array $options = array()) {

		foreach($this->addressBooks as $key => $value) {
			if ($value['id'] === $addressBookId) {
				unset($this->addressBooks[$key]);
			}
		}
		if(isset($this->contacts[$addressBookId])) {
			unset($this->contacts[$addressBookId]);
			return true;
		}
		return false;

	}

	function getContacts($addressBookId, array $options = array()) {

		$contacts = array();
		$omitdata = isset($options['omitdata']) ? $options['omitdata'] : false;
		$book = $this->getAddressBook($addressBookId);
		if(!$book) {
			return $contacts;
		}
		foreach($this->contacts[$addressBookId] as $contact) {
			$contact['permissions'] = $book['permissions'];
			$contact['owner'] = $book['owner'];
			if($omitdata) {
				unset($contact['carddata']);
			}
			$contacts[] = $contact;
		}

		return $contacts;

	}

	function getContact($addressBookId, $id, array $options = array()) {

		$book = $this->getAddressBook($addressBookId);
		if(!$book) {
			return null;
		}
		if (!isset($this->contacts[$addressBookId][$id])) {
			return null;
		}

		$contact = $this->contacts[$addressBookId][$id];
		$contact['permissions'] = $book['permissions'];
		$contact['owner'] = $book['owner'];

		return $contact;

	}

	function createContact($addressBookId, $contact, array $options = array()) {

		$id = \OC_Util::generateRandomBytes('4');
		$this->contacts[$addressBookId][$id] = array(
						'id' => $id,
						'displayname' => $contact->FN,
						'carddata' => $contact->serialize()
					);

		return $id;
	}

	function updateContact($addressBookId, $id, $contact, array $options = array()) {
		//echo __METHOD__ . $addressBookId .', ' . $id . ', ' . print_r($contact, true);
		$this->contacts[$addressBookId][$id] = array(
						'displayname' => $contact->FN,
						'carddata' => $contact->serialize()
					);

		return true;

	}

	function deleteContact($addressBookId, $id, array $options = array()) {

		if(isset($this->contacts[$addressBookId][$id])) {
			unset($this->contacts[$addressBookId][$id]);
			return true;
		}
		return false;

	}

	function numContacts($addressBookId) {
		return count($this->contacts[$addressBookId]);
	}
}
