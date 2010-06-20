<?php

require_once( ZEN_PATH . "/lib/Date.class.php");

class TransactionSQL {
	
	private $sqlQuery;
	private $id_t;
	private $siren;
	
	public function __construct(SQLQuery $sqlQuery,$id_t){
		$this->sqlQuery = $sqlQuery;
		$this->id_t = $id_t;
	}
	
	public function create($type,$etat,$objet){	
		
		$sql = "INSERT INTO transaction(id_t,type,etat,attente_traitement,date_changement_etat,objet) VALUES (?,?,?,1,now(),?)";
		$this->sqlQuery->query($sql,array($this->id_t,$type,$etat,$objet));
		
		$this->setEtat($etat);
	}
	
	public function addRole($siren,$role){
		$sql = "INSERT INTO transaction_role(id_t,siren,role) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,array($this->id_t, $siren,$role));
	}
	
	public function getInfo(){
		return $this->sqlQuery->fetchOneLine("SELECT * FROM transaction WHERE id_t=?",array($this->id_t));
	}
	
	public function getEtat(){
		return $this->sqlQuery->fetchAll("SELECT * FROM transaction_changement_etat WHERE id_t=? ORDER BY date DESC ",array($this->id_t));
	}
	
	public function getAllRole(){
		$sql = "SELECT * FROM transaction_role " . 
				" JOIN entite ON transaction_role.siren = entite.siren " . 
				" WHERE id_t=?  ";
		$param = array($this->id_t);
		if ($this->siren){
			$sql  .= " AND (role='emmeteur' OR transaction_role.siren=?) ";
			$param[] = $this->siren;
		}
		return $this->sqlQuery->fetchAll($sql,$param);
	}
	
	public function setEtat($etat){
		$now = date(Date::DATE_ISO);
		
		$sql = "INSERT INTO transaction_changement_etat(id_t,etat,date) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,array($this->id_t,$etat,$now));
		
		$sql = "UPDATE transaction SET etat=?,date_changement_etat=?,attente_traitement=1 WHERE id_t=?";
		$this->sqlQuery->query($sql,array($etat,$now,$this->id_t));
	}
	
	public function traitementOK(){
		$sql = "UPDATE transaction SET attente_traitement=0 WHERE id_t=?";
		$this->sqlQuery->query($sql,array($this->id_t));
	}
	
	public function getRole($role){
		$sql = "SELECT siren FROM transaction_role WHERE id_t=? AND role=?";
		return $this->sqlQuery->fetchOneValue($sql,array($this->id_t,$role));
		
	}
	
	public function isAuthorized($siren){
		$sql = "SELECT count(*) FROM transaction_role WHERE id_t=? AND siren=?";
		return $this->sqlQuery->fetchOneValue($sql,array($this->id_t,$siren));
	}
	
	public function restrictInformation($siren) {
		$this->siren = $siren;
	}
}