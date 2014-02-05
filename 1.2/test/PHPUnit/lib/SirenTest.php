<?php 
require_once __DIR__.'/../init.php';

class SirenTest extends PHPUnit_Framework_TestCase {

	private $siren;
	
	public function setUp(){
		$this->siren = new Siren();	
	}
	
	public function testGood(){
		$this->assertTrue($this->siren->isValid("493587273"));		
	}
	
	public function testBadLength(){
		$this->assertFalse($this->siren->isValid(""));		
	}
	
	public function testBad(){
		$this->assertFalse($this->siren->isValid("493587274"));		
	}
	
	public function testGenerate(){
		$this->assertTrue($this->siren->isValid($this->siren->generate()));
	}
	
}