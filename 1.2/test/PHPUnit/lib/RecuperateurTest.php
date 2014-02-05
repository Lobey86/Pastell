<?php 
require_once __DIR__.'/../init.php';

class RecuperateurTest extends PHPUnit_Framework_TestCase {	
	
	public function testRecupSimpe(){
		$recup = new Recuperateur(array('toto'=>'titi'));
		$this->assertEquals("titi",$recup->get('toto'));
		$this->assertEquals(false,$recup->get('valeur inexistante'));
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
	
	public function testGetInt(){
		$tab = array('toto'=>'test');
		$recup = new Recuperateur($tab);
		$this->assertEquals(0, $recup->getInt('toto'));
	}

	public function testGetTrim(){
		$tab = array('toto'=>' test ');
		$recup = new Recuperateur($tab);
		$this->assertEquals('test', $recup->get('toto'));
	}
	
	public function testGetNoTrim(){
		$tab = array('toto'=>' test ');
		$recup = new Recuperateur($tab);
		$this->assertEquals(' test ', $recup->getNoTrim('toto'));
	}
	
	public function testGetNoTrimDefault(){
		$tab = array('toto'=>' test ');
		$recup = new Recuperateur($tab);
		$this->assertEquals(false, $recup->getNoTrim('titi'));
	}
	
	public function testGetAll(){
		$tab = array('a'=>1,'b'=>42);
		$recup = new Recuperateur($tab);
		$all = $recup->getAll();
		$this->assertEquals($tab,$all);
	}
	
}
