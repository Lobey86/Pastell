<?php
require_once(dirname(__FILE__) . "/../web/init.php");

require_once('simpletest/autorun.php');
require_once('DBUnitTestCase.class.php');
require_once( PASTELL_PATH .'/lib/droit/RoleUtilisateur.class.php');
require_once( PASTELL_PATH .'/lib/entite/Entite.class.php');
require_once( PASTELL_PATH .'/lib/entite/EntiteCreator.class.php');

class RoleUtilisateurTest extends DBUnitTestCase {
	
	private $roleDroit;
	
	public function __construct(){
		parent::__construct();
		$this->sqlQuery->query(RoleUtilisateur::CREATE_SQL . " ENGINE = MEMORY;");
		$this->sqlQuery->query(Entite::CREATE_SQL . " ENGINE = MEMORY;");
		$this->sqlQuery->query(Journal::CREATE_SQL . " ENGINE = MEMORY;");
		
		$this->roleDroit = new RoleDroit();
	}

	
	public function testhasDroit(){
		$roleUtilisateur = new RoleUtilisateur($this->sqlQuery,$this->roleDroit);
		$roleUtilisateur->addRole(9999,"admin",0);
		$this->assertTrue($roleUtilisateur->hasDroit(9999,"entite:edition",0));
	}
	
	public function testhasNoDroit(){
		$roleUtilisateur = new RoleUtilisateur($this->sqlQuery,$this->roleDroit);
		$this->assertFalse($roleUtilisateur->hasDroit(9998,"entite:edition",0));
	}
	
	public function testCanEditEntite(){
		$entiteCreator = new EntiteCreator($this->sqlQuery, new Journal($this->sqlQuery,999));
		$id_e = $entiteCreator->create("123","test1",Entite::TYPE_COLLECTIVITE,0);
		$roleUtilisateur = new RoleUtilisateur($this->sqlQuery,$this->roleDroit);
		$this->assertFalse($roleUtilisateur->hasDroit(9998,"entite:edition",$id_e));
		
		$roleUtilisateur->addRole(9997,"admin",$id_e);
		$this->assertTrue($roleUtilisateur->hasDroit(9999,"entite:edition",$id_e));
		
	}
	
}