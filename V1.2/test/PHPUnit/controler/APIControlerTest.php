<?php

require_once __DIR__.'/../init.php';

class APIControlerTest extends PastellTestCase {

	/**
	 * @return APIControler
	 */
	private function getAPIControler(){
		return $this->getObjectInstancier()->APIControler;
	}
	
	public function testIndexAction(){
		$this->expectOutputRegex("##");
		$this->getAPIControler()->indexAction();
	}
	
}