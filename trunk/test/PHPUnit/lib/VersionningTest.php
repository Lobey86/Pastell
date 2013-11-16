<?php
require_once __DIR__.'/../init.php';
require_once 'vfsStream/vfsStream.php';

class VersionningTest extends PHPUnit_Framework_TestCase {

	private $revision_path;
	private $version_path;
	
	protected function setUp() {
		$testStream = vfsStream::setup('test');
		$testStreamUrl = vfsStream::url('test');
		$this->revision_path = $testStreamUrl."/revision.txt";
		$this->version_path  = $testStreamUrl."/version.txt";
		file_put_contents($this->revision_path ,'$Rev: 666 $');
		file_put_contents($this->version_path, '1.12');
	}
	
	private function getVersionning(){
		return new Versionning($this->version_path, $this->revision_path);
	}
	
	public function testRevisionFalse(){
		file_put_contents($this->revision_path ,'toto');
		$this->assertFalse($this->getVersionning()->getRevision());
	}
	
	public function testRevisionOK(){
		$versionning = new Versionning($this->revision_path , $this->version_path);
		$this->assertEquals(666, $this->getVersionning()->getRevision());
	}
	
	public function testVersion(){
		$versionning = new Versionning($this->revision_path , $this->version_path);
		$this->assertEquals("1.12", $this->getVersionning()->getVersion());
	}
	
	public function testAllInfo(){
		$this->assertEquals(array('version'=>'1.12','revision'=>'666', 'version-complete' => 'Version 1.12 - Révision  666'),
		$this->getVersionning()->getAllInfo());
	}	
}