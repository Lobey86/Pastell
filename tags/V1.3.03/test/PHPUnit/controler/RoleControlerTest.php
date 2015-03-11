<?php
require_once __DIR__.'/../init.php';

class RoleControlerTest extends PastellTestCase {

	public function __construct(){
		parent::__construct();
		$this->getRoleControler()->setDontRedirect(true);
	}
	
	/**
	 * @return RoleControler
	 */
	private function getRoleControler(){
		return $this->getObjectInstancier()->RoleControler;
	}
	
	public function reinitDatabaseOnSetup(){
		return true;
	}
	
	public function reinitFileSystemOnSetup(){
		return true;
	}
	
	public function setUp(){
		$this->getObjectInstancier()->Authentification->Connexion('admin',1);
		parent::setUp();
	}

	public function testIndexAction(){
		$this->expectOutputRegex("##");
		$this->getRoleControler()->indexAction();
	}
	
	public function testDetailAction(){
		$this->expectOutputRegex("##");
		$this->getRoleControler()->detailAction();
	}
	
	public function testEditionAction(){
		$this->expectOutputRegex("##");
		$this->getRoleControler()->editionAction();
	}
	
	public function testEditionAction2(){
		$this->expectOutputRegex("##");
		$_GET = array('role'=>'admin');
		$this->getRoleControler()->editionAction();
	}
	
	/**
	 * @expectedException LastMessageException
	 */
	public function testDoEditionAction(){
		$_POST = array('role'=>'test','libelle'=>'test');
		$this->getRoleControler()->doEditionAction();
	}
	
	/**
	 * @expectedException LastMessageException
	 */
	public function testDoDeleteAction(){
		$this->getRoleControler()->doDeleteAction();
	}
	
	/**
	 * @expectedException LastMessageException
	 */
	public function testDoDetailAction(){
		$_POST = array('role'=>'test','droit'=>array('system:lecture'=>'selected'));
		$this->getRoleControler()->doDetailAction();
	}
}