<?php 

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/FancyDate.class.php');

class FancyDateTest extends UnitTestCase {	
	
	const TIME_EXEMPLE = 225099900; //1977-02-18 08:45
	
	public function testGroupeJour(){
		$fancy = new FancyDate();
		$fancy->setRegroupement(FancyDate::JOUR);
		$this->assertEqual("18 fév 1977",$fancy->getFancy(self::TIME_EXEMPLE));
	}
	
	public function testGroupeMois(){
		$fancy = new FancyDate();
		$fancy->setRegroupement(FancyDate::MOIS);
		$this->assertEqual("fév 1977",$fancy->getFancy(self::TIME_EXEMPLE));		
	}
	
	public function testGroupeAnnee(){
		$fancy = new FancyDate();
		$fancy->setRegroupement(FancyDate::ANNEE);
		$this->assertEqual("1977",$fancy->getFancy(self::TIME_EXEMPLE));		
	}
	
	public function testIsToday(){
		$fancy = new FancyDate();		
		$this->assertTrue($fancy->isToday(time()));	
	}
	
	public function testisNotToday(){
		$fancy = new FancyDate();		
		$this->assertFalse($fancy->isToday(0));	
	}
	
	public function testAfficheToday(){
		$fancy = new FancyDate();
		$fancy->setToday(self::TIME_EXEMPLE);
		$this->assertEqual("08:45",$fancy->getFancy(self::TIME_EXEMPLE));
	}
	
	public function testAfficheYesterday(){
		$fancy = new FancyDate();
		$fancy->setToday(self::TIME_EXEMPLE + 86400);
		$this->assertEqual("18 fév",$fancy->getFancy(self::TIME_EXEMPLE));
	}
	
	public function testAfficheAnDernier(){
		$fancy = new FancyDate();
		$fancy->setToday(self::TIME_EXEMPLE + 86400 * 366);
		$this->assertEqual("18 fév 1977",$fancy->getFancy(self::TIME_EXEMPLE));
	}
	
	public function testAfficheYesterdayWithTime(){
		$fancy = new FancyDate();
		$fancy->alwaysShowTime(true);
		$fancy->setToday(self::TIME_EXEMPLE + 86400);
		$this->assertEqual("18 fév - 08:45",$fancy->getFancy(self::TIME_EXEMPLE));
	}
	
	public function testAfficheAnDernierWithTime(){
		$fancy = new FancyDate();
		$fancy->alwaysShowTime(true);		
		$fancy->setToday(self::TIME_EXEMPLE + 86400 * 366);
		$this->assertEqual("18 fév 1977 - 08:45",$fancy->getFancy(self::TIME_EXEMPLE));
	}
	
}