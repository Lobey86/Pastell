<?php 
require_once('simpletest/autorun.php');

class PasswordGeneratorTest extends UnitTestCase {

	public function testGetPassword(){
		$passwordGenerator = new PasswordGenerator();
		$password = $passwordGenerator->getPassword();
		$this->assertEqual(PasswordGenerator::NB_SIGNE_DEFAULT,strlen($password));	
		$this->assertWantedPattern("/^[".PasswordGenerator::SIGNE."]*$/",$password);
	}

	public function testSetPasswordLength(){
		$passwordGenerator = new PasswordGenerator();
		$password = $passwordGenerator->getPassword();
		$this->assertEqual(PasswordGenerator::NB_SIGNE_DEFAULT,strlen($password));	
	}
	
	public function testSetSignePassword(){
		$passwordGenerator = new PasswordGenerator();
		$passwordGenerator->setSigne("X");
		$password = $passwordGenerator->getPassword();
		$this->assertEqual("XXXXXXX",$password);
	}
}