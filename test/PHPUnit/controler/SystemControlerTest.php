<?php 
require_once __DIR__.'/../init.php';

class SystemControlerTest extends PastellTestCase {
	
	public function __construct(){
		parent::__construct();
		$this->getObjectInstancier()->Authentification->Connexion('admin',1);
		$this->getSystemControler()->setDontRedirect(true);
	}
	
	/**
	 * @return SystemControler
	 */
	private function getSystemControler(){
		return $this->getObjectInstancier()->SystemControler;
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
	public function testDoExtensionEditionAction() {
		$_POST['path'] = '/tmp/';
		$this->getSystemControler()->doExtensionEditionAction();
	}
	
	/**
	 * @expectedException LastErrorException
	 */
	public function testDoExtensionEditionActionFail() {
		$_POST['path'] = '';
		$this->getSystemControler()->doExtensionEditionAction();
	}

	public function testFluxDetailAction(){
		$_GET['id'] = 'actes-generique';
		$this->expectOutputRegex("##");
		$this->getSystemControler()->fluxDetailAction();
	}
	
	public function testIndex() {
		$this->expectOutputRegex("##");
		$this->getSystemControler()->indexAction();
	}
	
}