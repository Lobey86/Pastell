<?php
require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");
require_once( PASTELL_PATH . "/lib/base/Date.class.php");

class Journal {
	
	const CHANGEMENT_ETAT = 1;
	const NOTIFICATION = 2;
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function add($type,$id_t,$message){
		$now = date(Date::DATE_ISO);
		$proof= "";
		$sql = "INSERT INTO journal(type,id_t,message,date,preuve) VALUES (?,?,?,?,?)";
		$this->sqlQuery->query($sql,array($type,$id_t,$message,$now,$proof));
	}
	
	public function getAll($offset,$limit){
		$sql = "SELECT * FROM journal ORDER BY id_j DESC LIMIT $offset,$limit";
		return $this->sqlQuery->fetchAll($sql);
	}
	
	public function countAll(){
		return $this->sqlQuery->fetchOneValue("SELECT count(*) FROM journal");
	}
	
	public function getAllTransactionBySiren($siren,$offset,$limit){
			$sql = "SELECT journal.* FROM transaction_role "  .  
					" JOIN transaction ON transaction_role.id_t = transaction.id_t " . 
					" AND transaction_role.siren=? " . 
					" JOIN journal ON journal.id_t = transaction.id_t ".
					" ORDER BY id_j DESC LIMIT $offset,$limit";
		return $this->sqlQuery->fetchAll($sql,array($siren));		
	}	
	
	public function countBySiren($siren){
		$sql = "SELECT count(*) FROM transaction_role "  .  
					" JOIN transaction ON transaction_role.id_t = transaction.id_t " . 
					" AND transaction_role.siren=? " . 
					" JOIN journal ON journal.id_t = transaction.id_t ";
		return $this->sqlQuery->fetchOneValue($sql,array($siren));
	}
	
	public function getTypeAsString($type){
		$type_string = array(1=>"Changement d'état","Notification");
		return $type_string[$type];
	}
}