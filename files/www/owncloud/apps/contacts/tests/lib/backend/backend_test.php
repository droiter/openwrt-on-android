<?php
/**
 * Copyright (c) 2013 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts\Backend;

use Sabre\VObject\Reader;

require_once __DIR__ .'/mock.php';

class BackendTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var array
	*/
	protected $abinfo;

	/**
	* @var array
	*/
	protected $permissions = array(
		\OCP\PERMISSION_READ,
		\OCP\PERMISSION_CREATE,
		\OCP\PERMISSION_UPDATE,
		\OCP\PERMISSION_DELETE
	);

	/**
	* @var OCA\Contacts\Backend\AbstractBackend
	*/
	protected $backend;

	public function setUp() {

		$this->backend = new Mock('foobar');

	}

	public function tearDown() {
		unset($this->backend);
	}

	public function testHasContactMethodFor() {

		foreach($this->permissions as $permission) {
			$this->assertTrue($this->backend->hasContactMethodFor($permission));
		}

	}

	public function testHasAddressBookMethodFor() {

		foreach($this->permissions as $permission) {
			$this->assertTrue($this->backend->hasAddressBookMethodFor($permission));
		}

	}

	public function testgetAddressBooksForUser() {

		$this->assertEquals(1, count($this->backend->getAddressBooksForUser()));

	}

	public function testDeleteAddressBook() {

		$this->assertTrue($this->backend->deleteAddressBook('foo'));
		$this->assertEquals(array(), $this->backend->addressBooks);

	}

	public function testCreateAddressBook() {

		$id = $this->backend->createAddressBook(array('displayname' => 'bar'));

		$this->assertNotEquals(false, $id);

		$this->assertEquals(2, count($this->backend->getAddressBooksForUser()));

		$book = $this->backend->getAddressBook($id);

		$this->assertEquals('bar', $book['displayname']);

	}

	public function testCreateAddressBookFail() {

		// displayname must be provided.
		$id = $this->backend->createAddressBook(array('description' => 'foo bar'));

		$this->assertFalse($id);

	}

	public function testUpdateAddressBook() {

		$this->assertTrue(
			$this->backend->updateAddressBook('foo', array('displayname' => 'bar'))
		);

		$this->assertEquals('bar', $this->backend->addressBooks[0]['displayname']);

		return $this->backend;

	}

	public function testUpdateAddressBookFail() {

		$this->assertFalse(
			$this->backend->updateAddressBook('foo', array('description' => 'foo bar'))
		);

	}

	/**
	* @depends testUpdateAddressBook
	*/
	public function testGetAddressBook($backend) {

		$book = $backend->getAddressBook('foo');
		$this->assertEquals('bar', $book['displayname']);

	}

	public function testGetAddressBookFail() {

		$this->assertNull($this->backend->getAddressBook('bar'));

	}

	public function testGetLastModifiedAddressBook() {

		$this->assertNull($this->backend->lastModifiedAddressBook('foo'));

	}

	public function testGetContact() {

		$contact = $this->backend->getContact('foo', '123');
		$this->assertTrue(is_array($contact));
		$this->assertEquals('Max Mustermann', $contact['displayname']);
		$this->assertEquals('foobar', $contact['owner']);
		$this->assertEquals(\OCP\PERMISSION_ALL, $contact['permissions']);

	}

	public function testGetContactFail() {

		$this->assertNull($this->backend->getContact('foo', '1234'));

	}

	public function testCreateContact() {

		$carddata = file_get_contents(__DIR__ . '/../../data/test2.vcf');
		$vcard = Reader::read($carddata);
		$id = $this->backend->createContact('foo', $vcard);

		$this->assertNotEquals(false, $id);

		return $this->backend;
	}

	/**
	* @depends testCreateContact
	*/
	public function testGetContacts($backend) {

		$contacts = $backend->getContacts('foo');

		$this->assertCount(2, $contacts);

		$this->assertEquals('Max Mustermann', $contacts[0]['displayname']);
		$this->assertEquals('John Q. Public', $contacts[1]['displayname']);

	}

	public function testUpdateContact() {

		$carddata = file_get_contents(__DIR__ . '/../../data/test2.vcf');
		$vcard = Reader::read($carddata);

		$this->assertInstanceOf('OCA\\Contacts\\VObject\\VCard', $vcard);

		$this->assertTrue($this->backend->updateContact('foo', '123', $vcard));

		$contact = $this->backend->getContact('foo', '123');

		$this->assertEquals('John Q. Public', $contact['displayname']);

	}

	public function testDeleteContact() {

		$this->assertTrue($this->backend->deleteContact('foo', '123'));
		$this->assertEquals(array(), $this->backend->getContacts('foo'));

	}

}
