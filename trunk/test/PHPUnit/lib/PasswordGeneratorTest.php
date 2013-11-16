<?php

require_once __DIR__.'/../init.php';

class PasswordGeneratorTest extends PHPUnit_Framework_TestCase {

	public function testGetPassword(){
		$passwordGenerator = new PasswordGenerator();
		$password = $passwordGenerator->getPassword();
		$this->assertEquals(PasswordGenerator::NB_SIGNE_DEFAULT,strlen($password));
		$this->assertRegExp("/^[".PasswordGenerator::SIGNE."]*$/",$password);
	}

	public function testSetPasswordLength(){
		$passwordGenerator = new PasswordGenerator();
		$password = $passwordGenerator->getPassword();
		$this->assertEquals(PasswordGenerator::NB_SIGNE_DEFAULT,strlen($password));
	}

	public function testSetSignePassword(){
		$passwordGenerator = new PasswordGenerator();
		$passwordGenerator->setSigne("X");
		$password = $passwordGenerator->getPassword();
		$this->assertEquals("XXXXXXX",$password);
	}
}