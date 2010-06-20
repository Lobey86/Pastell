<?php 
require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/OutputXML.class.php');

class OutputXMLTest extends UnitTestCase {

	public function testEscapingCdata(){
		$txt = 'test';
		$outputXML = new OutputXML();
		$this->assertEqual('<![CDATA[test]]>',$outputXML->getCDATA($txt));
	}

	public function testHeader(){
		//Impossible à tester à cause de la fonction header();
	}
	
}