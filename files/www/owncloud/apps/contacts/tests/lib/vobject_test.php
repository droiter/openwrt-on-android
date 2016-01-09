<?php
/**
 * Copyright (c) 2013 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

class Test_VObjects extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
		\Sabre\VObject\Component::$classMap['VCARD']	= '\OCA\Contacts\VObject\VCard';
		\Sabre\VObject\Property::$classMap['CATEGORIES'] = 'OCA\Contacts\VObject\GroupProperty';

	}

	public function testCrappyVCard() {
		$carddata = file_get_contents(__DIR__ . '/../data/test3.vcf');
		$obj = \Sabre\VObject\Reader::read(
			$carddata,
			\Sabre\VObject\Reader::OPTION_IGNORE_INVALID_LINES
		);
		$obj->validate($obj::REPAIR|$obj::UPGRADE);

		$this->assertEquals('3.0', (string)$obj->VERSION);
		$this->assertEquals('Adèle Fermée', (string)$obj->FN);
		$this->assertEquals('Fermée;Adèle;;;', (string)$obj->N);
	}

	public function testEscapedParameters() {
		$carddata = file_get_contents(__DIR__ . '/../data/test6.vcf');
		$obj = \Sabre\VObject\Reader::read(
			$carddata,
			\Sabre\VObject\Reader::OPTION_IGNORE_INVALID_LINES
		);
		$obj->validate($obj::REPAIR|$obj::UPGRADE);

		$this->assertEquals('3.0', (string)$obj->VERSION);
		$this->assertEquals('Parameters;Escaped;;;', (string)$obj->N);
		$this->assertEquals('TEL;TYPE=PREF;TYPE=WORK;TYPE=VOICE:123456789' . "\r\n", $obj->TEL->serialize());
	}

	public function testGroupProperty() {
		$arr = array(
			'Home',
			'work',
			'Friends, Family',
		);

		$property = \Sabre\VObject\Property::create('CATEGORIES');
		$property->setParts($arr);

		// Test parsing and serializing
		$this->assertEquals('Home,work,Friends\, Family', $property->value);
		$this->assertEquals('CATEGORIES:Home,work,Friends\, Family' . "\r\n", $property->serialize());
		$this->assertEquals(3, count($property->getParts()));

		// Test add
		$property->addGroup('Coworkers');
		$this->assertTrue($property->hasGroup('coworkers'));
		$this->assertEquals(4, count($property->getParts()));
		$this->assertEquals('Home,work,Friends\, Family,Coworkers', $property->value);

		// Test remove
		$this->assertTrue($property->hasGroup('Friends, fAmIlY'));
		$property->removeGroup('Friends, fAmIlY');
		$this->assertEquals(3, count($property->getParts()));
		$parts = $property->getParts();
		$this->assertEquals('Coworkers', $parts[2]);

		// Test rename
		$property->renameGroup('work', 'Work');
		$parts = $property->getParts();
		$this->assertEquals('Work', $parts[1]);
		//$this->assertEquals(true, false);
	}
}