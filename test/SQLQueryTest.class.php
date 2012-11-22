<?php
require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/base/SQLQuery.class.php');

Mock::generate('ZLog');

class SQLQueryTest extends UnitTestCase {
	
	private $db;
	
	public function setUp(){
		$this->db = new SQLQuery("sqlite::memory:");
		$this->db->query("CREATE TABLE table1(col1 int,col2 int)");	
		$this->db->query("INSERT INTO table1(col1,col2) VALUES(2,3)");
		$this->db->query("INSERT INTO table1(col1,col2) VALUES(5,6)");		
	}
	
	public function testLog(){
		$zLog = new MockZLog();
		$this->db->setLog($zLog);
		$this->db->query("CREATE TABLE table2(col1,col2)");		
	}
	
	
	public function testSyntaxError(){
		try {
			$this->db->query("CREATE blutrepoir");
			$this->assertTrue(false);
		} catch (Exception $e){
			$this->assertWantedPattern('/syntax error/',
								$this->db->getLastError());
		}
	}
	
	public function testOneVal(){
		$col1 = $this->db->fetchOneValue("SELECT col1 FROM table1 WHERE col2=3");
		$this->assertEqual("2",$col1);		
		
	}	
	
	public function testFetchAll(){
		$all = $this->db->fetchAll("SELECT * FROM table1");
		$this->assertEqual(array(array('col1'=>2,'col2'=>3),
							array('col1'=>5,'col2'=>6)
							 ),$all);
	}
	
	public function testBindParam(){
		$col1 = $this->db->fetchOneValue("SELECT col1 FROM table1 WHERE col2=?",array(3));
		$this->assertEqual("2",$col1);				
	}
	
	public function testEchecRequete(){
		try {
			$this->db->fetchOneValue("SELECT col1 FROM table1 WHERE col2=?",array(2,4));
			$this->assertTrue(false);
		} catch (Exception $e) { /* OK */ }				
	}
	
	public function testNoResult(){
		$col1 = $this->db->fetchOneValue("SELECT col1 FROM table1 WHERE col2=?",array(42));
		$this->assertFalse($col1);
	}
	
	public function testPrepareAndExecute(){
		$r = $this->db->prepareAndExecute("SELECT col1 FROM table1 WHERE col2=?",array(3));
		$col1 = $this->db->fetch();
		$this->assertEqual("2",$col1['col1']);				
	}

}