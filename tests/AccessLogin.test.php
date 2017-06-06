<?php

namespace Tenth\AccessLogin\Tests;

use PHPUnit\Framework\TestCase;
use Tenth\AccessLogin;


class AccessLoginBadDataTests extends TestCase {

	public function test_invalidEmailReturnsFalse() {
		$this->assertFalse(AccessLogin::login('not an email address','password'));
	}

	public function test_falseEmailReturnsFalse() {
		$this->assertFalse(AccessLogin::login('invalid@test.tenth.org','password'));
	}

}