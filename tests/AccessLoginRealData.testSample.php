<?php

namespace Tenth\AccessLogin\Tests;

use PHPUnit\Framework\TestCase;
use Tenth\AccessLogin;


/** @noinspection PhpUndefinedClassInspection */
class AccessLoginRealDataTests extends TestCase {
	/** @var AccessLogin\AccessPerson[] */
	private static $__userA;

	/** @var AccessLogin\AccessPersonDetailed[] */
	private static $__userB;

	private $emailAddress = "EMAIL ADDRESS";
	private $password = "PASSWORD";


	public function test_badPasswordReturnsFalse() {
		$this->assertFalse(AccessLogin::login($this->emailAddress, "BadPassword"));
	}


	public function test_realInfoInit() {
		self::$__userA = AccessLogin::login($this->emailAddress, $this->password);
		$this->assertInternalType('array', self::$__userA);
	}


	public function test_detailed_realInfoInit() {
		self::$__userB = AccessLogin::login($this->emailAddress, $this->password, true);
		$this->assertInternalType('array', self::$__userB);
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getAvailableFields_type() {
		$this->assertInternalType('array', self::$__userB[0]->getAvailableFields());
	}


	/**
	 * @depends test_detailed_getAvailableFields_type
	 */
	public function test_detailed_getAvailableFields_length() {
		$this->assertGreaterThan(11, count(self::$__userB[0]->getAvailableFields()));
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getPhotoUrl() {
		$this->assertTrue(!!filter_var(self::$__userB[0]->PhotoUrl, FILTER_VALIDATE_URL));
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getFirstName() {
		$this->assertInternalType("string", self::$__userB[0]->FirstName);
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getGigglyPuff() {
		$this->expectException(AccessLogin\AccessException::class);
		$this->expectExceptionMessage("This AccessPersonDetailed object does not have a GigglyPuff data field.");
		self::$__userB[0]->GigglyPuff;
	}
}
