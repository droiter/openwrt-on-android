<?php
/**
 * Copyright (c) 2013 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts;

use Sabre\VObject\Reader;

require_once __DIR__ . '/backend/mock.php';

class AddressBookTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var array
	*/
	protected $abinfo;
	/**
	* @var OCA\Contacts\AddressBook
	*/
	protected $ab;
	/**
	* @var OCA\Contacts\Backend\AbstractBackend
	*/
	protected $backend;

	public function setUp() {

		\Sabre\VObject\Component::$classMap['VCARD']	= '\OCA\Contacts\VObject\VCard';

		$this->backend = new Backend\Mock('foobar');
		$this->abinfo = $this->backend->getAddressBook('foo');
		$this->ab = new AddressBook($this->backend, $this->abinfo);

	}

	public function tearDown() {
		unset($this->backend);
		unset($this->ab);
	}

	public function testGetDisplayName() {

		$this->assertEquals('d-name', $this->ab->getDisplayName());

	}

	public function testGetPermissions() {

		$this->assertEquals(\OCP\PERMISSION_ALL, $this->ab->getPermissions());

	}

	public function testGetBackend() {

		$this->assertEquals($this->backend, $this->ab->getBackend());

	}

	public function testGetChild() {

		$contact = $this->ab->getChild('123');
		$this->assertInstanceOf('OCA\\Contacts\\Contact', $contact);
		$this->assertEquals('Max Mustermann', $contact->getDisplayName());

	}

	public function testAddChild() {

		$carddata = file_get_contents(__DIR__ . '/../data/test2.vcf');
		$vcard = Reader::read($carddata);
		$id = $this->ab->addChild($vcard);
		$this->assertNotEquals(false, $id);

		return $this->ab;
	}

	public function testDeleteChild() {

		$this->assertTrue($this->ab->deleteChild('123'));
		$this->assertEquals(array(), $this->ab->getChildren());

	}

	public function testGetChildNotFound() {

		try {
			$contact = $this->ab->getChild('Nowhere');
		} catch(\Exception $e) {
			$this->assertEquals('Contact not found', $e->getMessage());
			$this->assertEquals(404, $e->getCode());
			return;
		}

		$this->fail('Expected Exception 404.');

	}

	/**
	* @depends testAddChild
	*/
	public function testGetChildren($ab) {

		$contacts = $ab->getChildren();

		$this->assertCount(2, $contacts);

		$this->assertEquals('Max Mustermann', $contacts[0]->getDisplayName());
		$this->assertEquals('John Q. Public', $contacts[1]->getDisplayName());

	}

	public function testDelete() {

		$this->assertTrue($this->ab->delete());
		$this->assertEquals(array(), $this->backend->addressBooks);

	}

	public function testGetLastModified() {

		$this->assertNull($this->ab->lastModified());

	}

	public function testUpdate() {

		$this->assertTrue(
			$this->ab->update(array('displayname' => 'bar'))
		);

		$this->assertEquals('bar', $this->backend->addressBooks[0]['displayname']);
		$props = $this->ab->getMetaData();

		return $this->ab;

	}

	/**
	* @depends testUpdate
	*/
	public function testGetMetaData($ab) {

		$props = $ab->getMetaData();
		$this->assertEquals('bar', $props['displayname']);

	}

	public function testArrayAccess() {

		$carddata = file_get_contents(__DIR__ . '/../data/test2.vcf');
		$vcard = Reader::read($carddata);

		$contact = $this->ab['123'];

		// Test get
		$this->assertTrue(isset($this->ab['123']));
		$this->assertInstanceOf('OCA\\Contacts\\Contact', $contact);
		$this->assertEquals('Max Mustermann', $contact->getDisplayName());

		// Test unset
		unset($this->ab['123']);

		$this->assertTrue(!isset($this->ab['123']));

		// Test set
		try {
			$this->ab[] = $vcard;
		} catch(\Exception $e) {
			return;
		}

		$this->fail('Expected Exception');

	}

	/**
	* @depends testAddChild
	*/
	public function testIterator($ab) {

		$count = 0;

		foreach($ab as $contact) {
			$this->assertInstanceOf('OCA\\Contacts\\Contact', $contact);
			$count += 1;
		}

		$this->assertEquals(2, $count);
	}

	/**
	* @depends testAddChild
	*/
	public function testCountable($ab) {

		$this->assertEquals(2, count($ab));

	}

}
