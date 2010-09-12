<?php 
require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/base/ZLog.class.php');
require_once("MockFile.class.php");

class ZLogTest extends UnitTestCase {
	
	public function setUp(){
		stream_wrapper_register("var", "MockFile");
	}
	
	public function tearDown(){
		stream_wrapper_unregister("var");		
	}

	public function testLog(){
		$log = new ZLog("var://test");
		$log->log("test",ZLog::ERROR);		
		$result = file_get_contents("var://test");
		$this->assertWantedPattern('/test/',$result);
		$this->assertWantedPattern('/'.ZLog::getLevelText(ZLog::ERROR).'/',$result);				
	}

	public function testDefaultLogLevel(){
		$log = new ZLog("var://test");
		$log->log("test");		
		$result = file_get_contents("var://test");
		$this->assertWantedPattern('/test/',$result);
		$this->assertWantedPattern('/'.ZLog::getLevelText(ZLog::DEFAULT_LOG_LEVEL).'/',$result);				
	}
	
	public function testNoLog(){
		$result_before = file_get_contents("var://test");
		
		$log = new ZLog("var://test");
		$log->setLogLevel(ZLog::ERROR);
		$log->log("test",ZLog::INFO);		
		$result_after = file_get_contents("var://test");
		$this->assertEqual($result_before,$result_after);
	}
	
	public function testUnableToWrite(){
		try {
			$log = new ZLog("http//impossible_decrire_ici");		
			$this->assertTrue(false);			
		} catch (Exception $e){ /* OK */ }
	}
	
	
}