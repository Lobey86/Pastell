<?php

class Annuaire {
	
	private $sqlQuery;
	private $id_e;
	
	public function __construct($sqlQuery,$id_e){
		$this->sqlQuery = $sqlQuery;
		$this->id_e = $id_e;
	}
	
	public function getUtilisateur(){
		$sql = "SELECT * FROM annuaire WHERE id_e=?";
		return $this->sqlQuery->fetchAll($sql,$this->id_e);
	}
	
}