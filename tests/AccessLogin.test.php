<?php

namespace Tenth\AccessLogin\Tests;

use PHPUnit\Framework\TestCase;
use Tenth\AccessLogin;


class AccessLoginTests extends TestCase {

	/* CREDENTIAL MANAGEMENT */
	protected static $email = null;
	protected static $password = null;

	public static function getEmail()
	{
		if (self::$email === null) {
			self::loadCredentials();
		}
		return self::$email;
	}

	public static function getPassword()
	{
		if (self::$password === null) {
			self::loadCredentials();
		}
		return self::$password;
	}

	private static function loadCredentials()
	{
		if (file_exists('tests/credentials.json')) {
			$creds          = json_decode( file_get_contents( 'tests/credentials.json' ) );
			self::$email    = $creds->email;
			self::$password = $creds->password;
		} else {
			self::$email = getenv("ACS_EMAIL");
			self::$password = getenv("ACS_PASSWORD");
		}
	}


	public function test_invalidEmailReturnsFalse() {
		$this->assertFalse(AccessLogin::login('not an email address','password'));
	}

	public function test_falseEmailReturnsFalse() {
		$this->assertFalse(AccessLogin::login('invalid@test.tenth.org','password'));
	}

	/** @var AccessLogin\AccessPerson[] */
	private static $__userA;

	/** @var AccessLogin\AccessPersonDetailed[] */
	private static $__userB;


	public function test_badPasswordReturnsFalse()
	{
		$this->assertFalse(AccessLogin::login(self::getEmail(), "BadPassword"));
	}


	public function test_realInfoInit()
	{
		self::$__userA = AccessLogin::login(self::getEmail(), self::getPassword());
		$this->assertSame('array', gettype(self::$__userA));
	}


	public function test_detailed_realInfoInit()
	{
		self::$__userB = AccessLogin::login(self::getEmail(), self::getPassword(), true);
		$this->assertSame('array', gettype(self::$__userB));
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getAvailableFields_type()
	{
		$this->assertSame('array', gettype(self::$__userB[0]->getAvailableFields()));
	}


	/**
	 * @depends test_detailed_getAvailableFields_type
	 */
	public function test_detailed_getAvailableFields_length()
	{
		$this->assertGreaterThan(11, count(self::$__userB[0]->getAvailableFields()));
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getPhotoUrl()
	{
		$this->assertTrue(!!filter_var(self::$__userB[0]->PhotoUrl, FILTER_VALIDATE_URL));
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getFirstName()
	{
		$this->assertSame("string", gettype(self::$__userB[0]->FirstName));
	}


	/**
	 * @depends test_detailed_realInfoInit
	 */
	public function test_detailed_getGigglyPuff()
	{
		$this->expectException(AccessLogin\AccessException::class);
		$this->expectExceptionMessage("This AccessPersonDetailed object does not have a GigglyPuff data field.");
		self::$__userB[0]->GigglyPuff;
	}

}