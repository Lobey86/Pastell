<?php
require_once __DIR__.'/../init.php';

class ActesSEDACG86Test extends PastellTestCase  {

	public function reinitDatabaseOnSetup(){
		return true;
	}
	
	public function reinitFileSystemOnSetup(){
		return true;
	}

	public function testAll(){
		$result = $this->getObjectInstancier()->ActionExecutorFactory->executeOnConnecteur(6,PastellTestCase::ID_U_ADMIN,'test-bordereau', true);
		$this->assertTrue($result,$this->getObjectInstancier()->ActionExecutorFactory->getLastMessage());
	}
}