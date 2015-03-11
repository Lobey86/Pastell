<?php

require_once __DIR__.'/../init.php';


class FichierCleValeurTest extends PastellTestCase {
	
	
	public function reinitFileSystemOnSetup(){
		return true;
	}
	
	public function testGetNonExistentsValue(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertFalse($fichierCleValeur->get("test1"));
	}
	
	public function testEmpty(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$info = $fichierCleValeur->getInfo();
		$this->assertEmpty($info);
	}
	
	public function conservationString($string){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->set("test1", $string);
		$fichierCleValeur->save();
		
		$fichierCleValeur = new FichierCleValeur($filePath);		
		$this->assertEquals($string, $fichierCleValeur->get("test1"));
	}
	
	public function testEmptyValue(){
		$this->conservationString("");
	}
	
	public function testString(){
		$this->conservationString("test");
	}
	
	public function testPlus(){
		$this->conservationString("+");
	}
	
	public function testPlus2(){
		$this->conservationString("+2");
	}
	
	public function testPlus2A(){
		$this->conservationString("+2A");
	}
	
	public function testQuote(){
		$this->conservationString("'test'");
	}
	
	public function testDoubleQuote(){
		$this->conservationString('"test"');
	}
	
	public function testHash(){
		$this->conservationString("#ceci n'est pas un commentaire");
	}
	
	public function testReturn(){
		$this->conservationString("retour\nà la ligne");
	}
	
	public function testAnother(){
		$this->conservationString("#ceci n\'est pas un commentaire");
	}
	
	public function testExists(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->set("test1", "premier");
		$fichierCleValeur->save();
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertTrue($fichierCleValeur->exists("test1"));
		$this->assertFalse($fichierCleValeur->exists("test2"));
	}
	
	public function testDeuxObjet(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->set("test1", "premier");
		$fichierCleValeur->set("test2","second");
		$fichierCleValeur->save();
		
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertEquals("premier", $fichierCleValeur->get("test1"));
		$this->assertEquals("second", $fichierCleValeur->get("test2"));
	}
	
	public function testMulti0(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->setMulti("test1", "premier");
		$fichierCleValeur->save();
			
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertEquals("premier", $fichierCleValeur->getMulti("test1"));
	}
	
	public function testMultiMany(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->setMulti("test1", "premier");
		$fichierCleValeur->setMulti("test1", "second",1);
		$fichierCleValeur->save();
			
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertEquals("premier", $fichierCleValeur->getMulti("test1"));
		$this->assertEquals("second", $fichierCleValeur->getMulti("test1",1));
	}
	
	public function testAddValue(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->addValue("test1", "premier");
		$fichierCleValeur->addValue("test1", "second");
		$fichierCleValeur->save();
			
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertEquals("premier", $fichierCleValeur->getMulti("test1"));
		$this->assertEquals("second", $fichierCleValeur->getMulti("test1",1));
	}
	
	
	public function testCount(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->addValue("test1", "premier");
		$fichierCleValeur->addValue("test1", "troisieme");
		$fichierCleValeur->addValue("test1", "second");
		$this->assertEquals(3, $fichierCleValeur->count("test1"));
	}
	
	public function testDelete(){
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		$fichierCleValeur = new FichierCleValeur($filePath);
		$fichierCleValeur->addValue("test1", "premier");
		$fichierCleValeur->addValue("test1", "troisieme");
		$fichierCleValeur->addValue("test1", "second");
		$fichierCleValeur->delete("test1", 1);
		$fichierCleValeur->save();
			
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertEquals("premier", $fichierCleValeur->getMulti("test1"));
		$this->assertEquals("second", $fichierCleValeur->getMulti("test1",1));
	}
	
	public function testUnescapeEmptyString() {
		$filePath = $this->getObjectInstancier()->workspacePath."/test.yml";
		file_put_contents($filePath,"test1: ");
		$fichierCleValeur = new FichierCleValeur($filePath);
		$this->assertEmpty($fichierCleValeur->get("test1"));
		
	}
	
	
}
