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
	
	public function getEntite($id_d){
		$sql = "SELECT * FROM document_entite JOIN entite ON document_entite.id_e = entite.id_e WHERE id_d=?";
		return $this->sqlQuery->fetchAll($sql,$id_d);
	}
	
	
	public function getRole($id_e,$id_d){
		$sql = "SELECT role FROM document_entite WHERE id_e=? AND id_d=? LIMIT 1";
		return  $this->sqlQuery->fetchOneValue($sql,$id_e,$id_d);
	}
	
	public function getEntiteWithRole($id_d,$role){
		$sql = "SELECT id_e FROM document_entite WHERE id_d=? AND role=? LIMIT 1";
		return  $this->sqlQuery->fetchOneValue($sql,$id_d,$role);
	}
	
}