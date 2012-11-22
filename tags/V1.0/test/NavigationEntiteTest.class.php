<?php

//PAS FINI

require_once(dirname(__FILE__) . "/../web/init.php");

require_once('simpletest/autorun.php');
require_once('DBUnitTestCase.class.php');
require_once( PASTELL_PATH .'/lib/droit/NavigationEntite.class.php');
require_once( PASTELL_PATH .'/lib/droit/RoleUtilisateur.class.php');
require_once( PASTELL_PATH .'/lib/entite/Entite.class.php');
require_once( PASTELL_PATH .'/lib/entite/EntiteCreator.class.php');

class NavigationPossibleTest extends DBUnitTestCase {
	
	
	public function __construct(){
		
		parent::__construct();
		
		$this->sqlQuery->query(RoleUtilisateur::CREATE_SQL . " ENGINE = MEMORY;");
		$this->sqlQuery->query(Entite::CREATE_SQL . " ENGINE = MEMORY;");
		$this->sqlQuery->query(Journal::CREATE_SQL . " ENGINE = MEMORY;");
		
		$this->roleDroit = new RoleDroit();
		
		$roleUtilisateur = new RoleUtilisateur($this->sqlQuery,$this->roleDroit);
		$roleUtilisateur->addRole(9999,"admin",0);
		
		
	}
	
	public function testAdminGal(){
		
		$navigationEntite = new NavigationEntite($this->sqlQuery,999,0);
		
		$list = $navigationEntite->getEntiteList();
		
		$this->assertEqual(array("toto"),$list);
		
	}
	
	
}