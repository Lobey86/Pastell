<?php 


require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/PageDecorator.class.php');

class PageDecoratorTest extends UnitTestCase {	
	
	private $result;
	
	public function setUp(){
		$pageDecorator = new PageDecorator('mon titre');
		$pageDecorator->addCSS("mon css");
		$pageDecorator->addJavascript("mon javascript");
		
		ob_start();
		$pageDecorator->haut();
		$pageDecorator->bas(3.14);
		$this->result = ob_get_contents();
		ob_end_clean();	
	}
	
	public function testCoverHaut(){
		$this->assertPattern('/mon titre/',$this->result);
		$this->assertPattern('/mon css/',$this->result);
		$this->assertPattern('/mon javascript/',$this->result);
	}
	
	public function testCoverBas(){
		$this->assertPattern('/3.14/',$this->result);
	}
	
}