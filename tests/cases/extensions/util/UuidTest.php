<?php

namespace li3_hansd\tests\cases\extensions\util;

use li3_hansd\extensions\util\UUID;

class UuidTest extends \lithium\test\Unit {

	public function testBasic() {
		$uuid1 = UUID::generate();
		$uuid2 = UUID::generate();

		$this->assertNotEqual($uuid1, $uuid2);
		$this->assertTrue(is_string($uuid1));
		$this->assertTrue(UUID::isValid($uuid1));
	}

	public function testValueUnique() {
		$uuid1 = new UUID();
		$uuid2 = new UUID();
		$this->assertNotEqual($uuid1, $uuid2);
	}

	public function _testValueString($result) {
		$this->assertTrue(is_string($result));
		$this->assertTrue(UUID::isValid($result));
	}

	public function testValueOutput() {
		$uuid = new UUID();
		$result = $uuid();
		$this->_testValueString($result);
		$result = '{' . $uuid . '}';
		$this->_testValueString($result);
		$result = '' . $uuid;
		$this->_testValueString($result);
	}

	public function testBinary() {
		$uuid = new UUID();
		$result = $uuid->binary();
		$this->assertNotEqual($uuid(), $result);
		$this->assertEqual($uuid, UUID::asString($result));
	}
}

