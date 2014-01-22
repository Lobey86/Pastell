<?php 
require_once __DIR__.'/../init.php';

require_once PASTELL_PATH."/connecteur/FakeGED/FakeGED.class.php";

class FakeGEDTest extends PastellTestCase  {

	/**
	 * @return FakeTdT
	 */
	public function getFakeGED(){
		return $this->getObjectInstancier()->FakeGED;
	}

	public function testAll(){
		$this->getFakeGED()->createFolder(false,false,false);
	}
}