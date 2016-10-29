<?php

use PHPUnit\Framework\TestCase;
use TenthPres\AccessLogin;


class AccessLoginBadDataTests extends TestCase {

	public function test_invalidEmailReturnsFalse() {
		$this->assertFalse(AccessLogin::login('not an email address','password'));
	}

	public function test_falseEmailReturnsFalse() {
		$this->assertFalse(AccessLogin::login('invalid@test.tenth.org','password'));
	}

}