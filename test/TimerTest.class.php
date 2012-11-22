<?php

require_once('simpletest/autorun.php');

class TimerTest extends UnitTestCase {
	
	public function testAll(){
		$timer = new Timer();
		$this->assertTrue($timer->getElapsedTime() > 0);
	}
}