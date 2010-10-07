<?php


require_once( PASTELL_PATH . "/lib/journal/Journal.class.php");
require_once( PASTELL_PATH . "/lib/notification/NotificationMail.class.php");
require_once( PASTELL_PATH . "/lib/base/Date.class.php");

class DocumentAction {
	
	const CREATE_TABLE = "CREATE TABLE `pastell`.`document_action` (
`id_d` VARCHAR( 16 ) NOT NULL ,
`action` VARCHAR( 16 ) NOT NULL ,
`date` DATETIME NOT NULL ,
`id_e` INT NOT NULL ,
`id_u` INT NOT NULL
)";
	
	private $sqlQuery;
	private $id_d;
	private $id_e;
	private $id_u;
	
	private $journal;
	
	public function __construct($sqlQuery,Journal $journal,$id_d,$id_e,$id_u){
		$this->sqlQuery = $sqlQuery;
		$this->id_d = $id_d;
		$this->id_u = $id_u;
		$this->id_e = $id_e;
		$this->journal = $journal;
	}
	
	public function addAction($action){	
		$now = date(Date::DATE_ISO);
		$sql = "INSERT INTO document_action(id_d,date,action,id_e,id_u) VALUES (?,?,?,?,?)";
		$this->sqlQuery->query($sql,$this->id_d,$now,$action,$this->id_e,$this->id_u);
				
		$sql = " UPDATE document SET last_action=? WHERE id_d=?";
		$this->sqlQuery->query($sql,$action,$this->id_d);
		
		
		$sql = "SELECT id_a FROM document_action WHERE id_d=? AND date=? AND action=? AND id_e=? AND id_u=?";
		return $this->sqlQuery->fetchOneValue($sql,$this->id_d,$now,$action,$this->id_e,$this->id_u);
	}
	
	public function getLastAction(){
		$sql = "SELECT action FROM document_action WHERE id_d=? ORDER BY date DESC LIMIT 1";
		return $this->sqlQuery->fetchOneValue($sql,$this->id_d);
	}
	
	
	
}