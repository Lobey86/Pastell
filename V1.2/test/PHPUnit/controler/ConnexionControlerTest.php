<?php

require_once __DIR__.'/../init.php';

class ConnexionControlerTest extends PastellTestCase {

	public function __construct(){
		parent::__construct();
		$this->getConnexionControler()->setDontRedirect(true);
	}

	/**
	 * @return ConnexionControler
	 */
	private function getConnexionControler(){
		return $this->getObjectInstancier()->ConnexionControler;
	}

	public function reinitDatabaseOnSetup(){
		return true;
	}

	public function reinitFileSystemOnSetup(){
		return true;
	}
	
	/**
	 * @expectedException LastMessageException
	 */
	public function testNotConnected() {
		$this->getObjectInstancier()->Authentification->deconnexion();
		$this->getConnexionControler()->verifConnected();
	}
	
	public function testConnexion(){
		$this->getObjectInstancier()->Authentification->Connexion('admin',1);
		$this->assertTrue($this->getConnexionControler()->verifConnected());
	}
	
	public function testConnexionAction(){
		$this->expectOutputRegex("#Merci de vous identifier#");
		$this->getConnexionControler()->connexionAction();
	}
	
	public function testConnexionAdminAction(){
		$this->expectOutputRegex("#Merci de vous identifier#");
		$this->getConnexionControler()->connexionAdminAction();
	}
	
	public function testOublieIdentifiant(){
		$this->expectOutputRegex("##");
		$this->getConnexionControler()->oublieIdentifiantAction();
	}

	public function testChangementMdpAction(){
		$this->expectOutputRegex("##");
		$this->getConnexionControler()->changementMdpAction();
	}
	
	public function testChangementNoDroitAction(){
		$this->expectOutputRegex("##");
		$this->getConnexionControler()->noDroitAction();
	}
	
	public function testCasErrorAction(){
		$this->expectOutputRegex("##");
		$this->getConnexionControler()->casErrorAction();
	}
	
	/**
	 * @expectedException LastMessageException
	 */
	public function testLogoutAction(){
		$this->getConnexionControler()->logoutAction();
	}
	
	
	

}