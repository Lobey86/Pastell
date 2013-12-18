<?php
require_once __DIR__.'/../init.php';

class LastUpstartTest extends PastellTestCase {

	public function setUp(){
		parent::setUp();
		$upstart_touch_file = $this->getObjectInstancier()->upstart_touch_file;
		if (file_exists($upstart_touch_file)) {
			unlink($upstart_touch_file);
		}	
	}
	
	/**
	 *	@return LastUpstart
	 */
	public function getLastUpstart() {
		return $this->getObjectInstancier()->LastUpstart;
	}
	
	public function testConstuct(){
		$this->assertInstanceOf("LastUpstart",$this->getLastUpstart());
	}
	
	public function testGetLastMtime(){
		$this->assertFalse($this->getLastUpstart()->getLastMtime());
	}
	
	public function testLastTimeNow(){
		$this->getLastUpstart()->updateMtime();
		$this->assertTrue( !! $this->getLastUpstart()->getLastMtime());
	}
	
	public function testHasWarning(){
		$this->assertTrue($this->getLastUpstart()->hasWarning());
	}
	
	public function testHasntWarning(){
		$this->getLastUpstart()->updateMtime();
		$this->assertFalse($this->getLastUpstart()->hasWarning());
	}
}
