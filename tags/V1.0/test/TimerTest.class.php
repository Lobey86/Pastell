<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/base/Timer.class.php');

class TimerTest extends UnitTestCase {
	
	public function testAll(){
		$timer = new Timer();
		$this->assertTrue($timer->getElapsedTime() > 0);
	}
}