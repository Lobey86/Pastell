<?php 


require_once(__DIR__.'/../../lib/Recuperateur.class.php');

class RecuperateurTest extends PHPUnit_Framework_TestCase {	
	
	public function testRecupSimpe(){
		$recup = new Recuperateur(array('toto'=>'titi'));
		$this->assertEquals("titi",$recup->get('toto'));
		$this->assertNull($recup->get('valeur inexistante'));
		$this->assertEquals("titi",$recup->get('valeur inexistante','titi'));
	}

	public function testRequest(){
		$recup = new Recuperateur();
	}

	public function testTableau(){
		$value = array(3,45,32);
		$tab = array('toto'=> $value);	
		$recup = new Recuperateur($tab);
		$this->assertEquals($value,$recup->get('toto'));			
	}

}
