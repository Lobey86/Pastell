<?php 
require_once __DIR__.'/../init.php';

class ExtensionSQLTest extends PastellTestCase {

	const PATH_EXEMPLE = "/tmp/test";
	
	/**
	 * @return ExtensionSQL
	 */
	private function getExtensionSQL(){
		return $this->getObjectInstancier()->ExtensionSQL;
	}
	
	public function reinitDatabaseOnSetup(){
		return true;
	}
	
	public function reinitFileSystemOnSetup(){
		return true;
	}
	
	public function testGetInfo() {		
		$info = $this->getExtensionSQL()->getInfo(1);
		$this->assertEquals(1,$info['id_e']);
	}
	
	public function testGetAll() {
		$info = $this->getExtensionSQL()->getAll();
		$this->assertEquals(2,count($info));
	}
	
	public function testUpdate() {
		$this->getExtensionSQL()->edit(1,self::PATH_EXEMPLE);
		$info = $this->getExtensionSQL()->getInfo(1);
		$this->assertEquals(self::PATH_EXEMPLE,$info['path']);
	}
	
	public function testInsert() {
		$this->getExtensionSQL()->edit(false,self::PATH_EXEMPLE);
		$info = $this->getExtensionSQL()->getInfo(3);
		$this->assertEquals(self::PATH_EXEMPLE,$info['path']);
	}
	
	public function testDelete() {
		$this->getExtensionSQL()->delete(1);
		$info = $this->getExtensionSQL()->getInfo(1);
		$this->assertFalse($info);
	}	
}