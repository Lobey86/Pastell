<?php

class EntiteRelation {
	
	const IS_FOURNISSEUR = "is_fournisseur";
	
	public function __construct($sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function addRelation($id_e1,$relation,$id_e2){
		$sql = "INSERT INTO entite_relation(id_e1,relation,id_e2) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,$id_e1,$relation,$id_e2);
	}
	
	public function getFromRelation($id_e1,$relation){
		$sql = "SELECT id_e2 FROM entite_relation WHERE id_e1=? AND relation= ? LIMIT 1";
		return $this->sqlQuery->fetchOneValue($sql,$id_e1,$relation);
	}
	
}