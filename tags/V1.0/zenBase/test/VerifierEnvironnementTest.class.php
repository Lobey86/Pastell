<?php 
require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/VerifierEnvironnement.class.php');

class VerifierEnvironnementTest extends UnitTestCase {

	public function testVersion(){
		$v = new VerifierEnvironnement();
		$v->setCurrentPHPVersion("5.2.4-2ubuntu5.7");
		$this->assertTrue($v->isPHPVersionOK("4.3"));
		$this->assertTrue($v->isPHPVersionOK("5.0"));
		$this->assertFalse($v->isPHPVersionOK("5.3"));
		$this->assertFalse($v->isPHPVersionOK("6.0"));		
		
		$v->setCurrentPHPVersion("4.3.2.1");
		$this->assertFalse($v->isPHPVersionOK("5.0"));
		$this->assertFalse($v->isPHPVersionOK("6.0"));		
	}
}