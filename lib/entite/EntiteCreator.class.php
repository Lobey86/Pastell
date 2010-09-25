<?php

require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");
require_once( PASTELL_PATH . "/lib/base/Date.class.php");

class EntiteCreator {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}

	
	public function create($siren,$denomination,$type,$entite_mere = 0){
		$sql = "INSERT INTO entite(siren,denomination,type,entite_mere,date_inscription) " . 
				" VALUES (?,?,?,?,now())";
		$this->sqlQuery->query($sql,$siren,$denomination,$type,$entite_mere);
		
		$sql = "SELECT id_e FROM entite WHERE siren = ? ";
		return $this->sqlQuery->fetchOneValue($sql,$siren);
	}
	
	
}