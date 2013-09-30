<?php 
require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/Siren.class.php');

class SirenTest extends UnitTestCase {

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