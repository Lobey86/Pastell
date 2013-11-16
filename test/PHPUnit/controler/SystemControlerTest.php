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
	
	public function setUp(){
		parent::setUpWithDBReinit();
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
}