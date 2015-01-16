<?php
require_once __DIR__.'/../init.php';

class ManifestReaderTest extends PHPUnit_Framework_TestCase {

	private function getManifestReader(){
		return new ManifestReader(new YMLLoader(), __DIR__."/../fixtures/manifest.yml");
	}
	
	public function testRevisionOK(){
		$this->assertEquals(679, $this->getManifestReader()->getRevision());
	}
	
	public function testVersion(){
		$this->assertEquals("1.1.4", $this->getManifestReader()->getVersion());
	}
	
	public function testAllInfo(){
		$info =$this->getManifestReader()->getInfo();
		$this->assertEquals(679, $info['revision']);
	}	
}