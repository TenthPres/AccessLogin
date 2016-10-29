<?php

use PHPUnit\Framework\TestCase;
use Tenth\AccessLogin\AccessPerson;


class AccessPersonBadDataTests extends TestCase
{
	private static $__personObject = null;
	private static function getPersonObject() {
		if (self::$__personObject == null)
			self::$__personObject = new AccessPerson([
				'FullName' => 'Full Name',
				'UserName' => 'UserName',
				'SiteNumber' => '12345',
				'SiteName' => 'My Church'
			]);
		return self::$__personObject;
	}

	public function test_personHasFullName() {
		$this->assertClassHasAttribute('FullName', AccessPerson::class);
	}

	public function test_personHasUserName() {
		$this->assertClassHasAttribute('UserName', AccessPerson::class);
	}

	public function test_personHasSiteNumber() {
		$this->assertClassHasAttribute('SiteNumber', AccessPerson::class);
	}

	public function test_personHasSiteName() {
		$this->assertClassHasAttribute('SiteName', AccessPerson::class);
	}

	public function test_personConstructor_FullName() {
		$this->assertEquals('Full Name',
			self::getPersonObject()->FullName);
	}

	public function test_personConstructor_UserName() {
		$this->assertEquals('UserName',
			self::getPersonObject()->UserName);
	}

	public function test_personConstructor_SiteNumber() {
		$this->assertEquals('12345',
			self::getPersonObject()->SiteNumber);
	}

	public function test_personConstructor_SiteName() {
		$this->assertEquals('My Church',
			self::getPersonObject()->SiteName);
	}
}