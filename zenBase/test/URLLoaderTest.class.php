<?php 
require_once('simpletest/autorun.php');
require_once('simpletest/mock_objects.php');

require_once(dirname(__FILE__).'/../lib/URLLoader.class.php');
require_once(dirname(__FILE__).'/../lib/ZLog.class.php');

Mock::generate('ZLog');

class URLLoaderTest extends UnitTestCase {
	
	private $loader;
	private $fixturePath;
	
	public function setUp(){
		$this->loader = new URLLoader();
		$php_self = $_SERVER['PHP_SELF'];
		$this->fixturePath = 'http://' . $_SERVER['HTTP_HOST'] . substr($php_self,0, strrpos($php_self,'/'))  .  "/fixture/" ;		
	}
	
	public function testLoad(){
		$content =  $this->loader->getContent($this->fixturePath."page.html");	
		$html = file_get_contents(dirname(__FILE__)."/fixture/page.html");			
		$this->assertNotNull($content);
		$this->assertNotNull($this->loader->getLastTime());
		$this->assertEqual(strlen($html),$this->loader->getLastSize());
		$this->assertTrue($content);
		$this->assertEqual($html,$content);
	}
	
	public function testNotFound(){
		$content =  $this->loader->getContent($this->fixturePath.'/page_qui_nexiste_pas');		
		$this->assertFalse($content);
		$this->assertEqual("404",$this->loader->getLastError());
	}
	
	public function testServerNotFound(){
		$content =  $this->loader->getContent('http://serveurquiexistepas.sigmalis.com/');		
		$this->assertFalse($content);
		$this->assertWantedPattern("/Couldn't resolve host/",$this->loader->getLastError());
	}	
	
	public function testRedirect(){
		$this->loader->setFollowLocation(true);
		$content =  $this->loader->getContent($this->fixturePath."redirect.php");
		$this->assertNotNull($content);
		$this->assertNotNull($this->loader->getLastTime());
		$this->assertTrue($content);
		$this->assertWantedPattern("/ceci est une page/",$content);				
	}
	
	public function testNotRedirect(){
		$content =  $this->loader->getContent($this->fixturePath."redirect.php");
		$this->assertFalse($content);
		$this->assertEqual("302",$this->loader->getLastError());
	}
	
	public function testLog(){
		$zLog = new MockZLog();
		$this->loader->setLog($zLog);
		$content =  $this->loader->getContent($this->fixturePath."page.html");	
	}
	
}
