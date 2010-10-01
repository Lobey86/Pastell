<?php

require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");
require_once( PASTELL_PATH . "/lib/base/Date.class.php");

class EntiteCreator {
	
	private $sqlQuery;
	
	private $journal;
	
	public function __construct(SQLQuery $sqlQuery, Journal $journal){
		$this->sqlQuery = $sqlQuery;
		$this->journal = $journal;
	}
	
	public function create($siren,$denomination,$type,$entite_mere = 0){
		$date_inscription = date(Date::DATE_ISO);
		$sql = "INSERT INTO entite(siren,denomination,type,entite_mere,date_inscription) " . 
				" VALUES (?,?,?,?,?)";
		$this->sqlQuery->query($sql,$siren,$denomination,$type,$entite_mere,$date_inscription);
		
		$sql = "SELECT id_e FROM entite WHERE siren = ? AND denomination=? AND type=? AND entite_mere=? AND date_inscription=?";
		$id_e =  $this->sqlQuery->fetchOneValue($sql,$siren,$denomination,$type,$entite_mere,$date_inscription);
	
		$this->journal->add(Journal::MODIFICATION_ENTITE,$id_e,0,"creation","");
		
		return $id_e;
	}
	
	
}