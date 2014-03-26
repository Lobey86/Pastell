<?php 
require_once __DIR__.'/../init.php';

require_once PASTELL_PATH."/connecteur/FakeTdt/FakeTdT.class.php";

class FakeTdtTest extends PastellTestCase  {
	
	public function reinitDatabaseOnSetup(){
		return true;
	}
	
	public function reinitFileSystemOnSetup(){
		return true;
	}

	/**
	 * @return FakeTdT
	 */
	public function getFakeTdT(){
		return $this->getObjectInstancier()->FakeTdT;
	}
	
	public function testGetStatus(){
		$this->assertEquals('FakeTdT',$this->getFakeTdT()->getLogicielName());
	}
}