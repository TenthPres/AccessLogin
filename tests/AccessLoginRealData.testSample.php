<?php

use PHPUnit\Framework\TestCase;
use Tenth\AccessLogin;


class AccessLoginRealDataTests extends TestCase {
	private $__userA;
	private $__userB;

	private $emailAddress = "EMAIL ADDRESS";
	private $password = "PASSWORD";

	public function test_realInfoInit() {
		$this->__userA = AccessLogin::login($this->emailAddress, $this->password);

		$this->assertInternalType('array', $this->__userA);
	}

	public function test_realInfoInitDetailed() {
		$this->__userB = AccessLogin::login($this->emailAddress, $this->password, true);

		$this->assertInternalType('array', $this->__userB);
	}
}
