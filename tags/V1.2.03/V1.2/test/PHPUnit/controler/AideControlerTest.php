<?php

require_once __DIR__.'/../init.php';



class AideControlerTest extends PastellTestCase {

	/**
	 * @return AideControler
	 */
	private function getAideControler(){
		return $this->getObjectInstancier()->AideControler;
	}
	
	public function testIndex(){
		$this->expectOutputRegex("##");
		$this->getAideControler()->indexAction();
	}

	
}