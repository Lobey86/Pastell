<?php

require_once __DIR__.'/../init.php';


class APIActionTest extends PastellTestCase {
	
	public function reinitFileSystemOnSetup(){
		return true;
	}
	
	public function testVersion(){
		$apiAction = new APIAction($this->getObjectInstancier(), 1);
		$version = $apiAction->version();
		$this->assertEquals('1.1.4', $version['version']);
	}
	
}