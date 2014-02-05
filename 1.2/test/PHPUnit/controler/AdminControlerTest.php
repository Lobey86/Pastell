<?php

require_once __DIR__.'/../init.php';



class AdminControlerTest extends PastellTestCase {

	/**
	 * @return AdminControler
	 */
	private function getAdminControler(){
		return $this->getObjectInstancier()->AdminControler;
	}
	
	
	public function testCreateAdmin() {
		$this->assertTrue($this->getAdminControler()->createAdmin('admin2','admin','admin@sigmalis.com'));
	}
	
	public function testCreateAdminFail(){
		$this->assertFalse($this->getAdminControler()->createAdmin('admin','admin','admin@sigmalis.com'));
	}
	
	public function testFixDroit() {
		$this->getAdminControler()->fixDroit();
	}

	
}