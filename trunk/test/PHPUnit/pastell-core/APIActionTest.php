<?php

require_once __DIR__.'/../init.php';


class APIActionTest extends PastellTestCase {
	
	public function testVersion(){
		$apiAction = new APIAction($this->getObjectInstancier(), 1);
		$version = $apiAction->version();
		$this->assertEquals('1.12', $version['version']);
	}
	
}