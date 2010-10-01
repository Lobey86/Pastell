<?php

class DocumentEntite {
	
	public function __construct($sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getDocument($id_e,$type){
		$sql = "SELECT * FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " . 
				" ORDER BY document.modification DESC";	
		return $this->sqlQuery->fetchAll($sql,$id_e,$type);
	}
	
	public function addRole($id_d,$id_e,$role){
		if ($this->hasRole($id_d,$id_e,$role)){
			return;
		}
		$sql = "INSERT INTO document_entite (id_d,id_e,role) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,$id_d,$id_e,$role);
	}
	
	public function hasRole($id_d,$id_e,$role){
		$sql = "SELECT count(*) FROM document_entite WHERE id_d=? AND id_e=? AND role= ?";
		return $this->sqlQuery->fetchOneValue($sql,$id_d,$id_e,$role);
	}
	
}