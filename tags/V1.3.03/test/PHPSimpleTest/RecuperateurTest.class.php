<?php 

require_once('simpletest/autorun.php');

class RecuperateurTest extends UnitTestCase {	
	
	public function testRecupSimpe(){
		$recup = new Recuperateur(array('toto'=>'titi'));
		$this->assertEqual("titi",$recup->get('toto'));
		$this->assertNull($recup->get('valeur inexistante'));
		$this->assertEqual("titi",$recup->get('valeur inexistante','titi'));
	}

	public function testRequest(){
		$recup = new Recuperateur();
	}

	public function testTableau(){
		$value = array(3,45,32);
		$tab = array('toto'=> $value);	
		$recup = new Recuperateur($tab);
		$this->assertEqual($value,$recup->get('toto'));			
	}

}
