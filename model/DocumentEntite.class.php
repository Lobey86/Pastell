<?php

class DocumentEntite extends SQL {
	
	public function getDocument($id_e,$type){
		$sql = "SELECT * FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " . 
				" ORDER BY document.modification DESC";	
		return $this->query($sql,$id_e,$type);
	}
	
	public function addRole($id_d,$id_e,$role){
		if ($this->hasRole($id_d,$id_e,$role)){
			return;
		}
		$sql = "INSERT INTO document_entite (id_d,id_e,role) VALUES (?,?,?)";
		$this->query($sql,$id_d,$id_e,$role);
	}
	
	public function hasRole($id_d,$id_e,$role){
		$sql = "SELECT count(*) FROM document_entite WHERE id_d=? AND id_e=? AND role= ?";
		return $this->queryOne($sql,$id_d,$id_e,$role);
	}
	
	public function getEntite($id_d){
		$sql = "SELECT * FROM document_entite JOIN entite ON document_entite.id_e = entite.id_e WHERE id_d=?";
		return $this->query($sql,$id_d);
	}
	
	public function getRole($id_e,$id_d){
		$sql = "SELECT role FROM document_entite WHERE id_e=? AND id_d=? LIMIT 1";
		return  $this->queryOne($sql,$id_e,$id_d);
	}
	
	public function getEntiteWithRole($id_d,$role){
		$sql = "SELECT id_e FROM document_entite WHERE id_d=? AND role=? LIMIT 1";
		return  $this->queryOne($sql,$id_d,$role);
	}
	
	public function getFromAction($type,$action){
		$sql = "SELECT * FROM document_entite " . 
				" JOIN document ON document_entite.id_d = document.id_d " . 
				" WHERE type = ? AND document_entite.last_action=?"; 
		return $this->query($sql,$type,$action);
	}
	
	public function getAll($id_e){
		$sql = "SELECT * FROM document_entite " .
				" JOIN document ON document_entite.id_d=document.id_d " .
				" WHERE id_e=?";
		return $this->query($sql,$id_e);
	}
	
	public function getAllByFluxAction($flux,$action_from){
			$sql = "SELECT * FROM document_entite " .
				" JOIN document ON document_entite.id_d=document.id_d " .
				" JOIN entite ON document_entite.id_e = entite.id_e " .
				" WHERE document.type=? AND last_action=?";
		return $this->query($sql,$flux,$action_from);
	}
	
	public function fixAction($flux,$action_from,$action_to){
		$sql = "UPDATE document_entite " .
				" JOIN document ON document_entite.id_d=document.id_d " .
				" SET last_action=?" . 
				" WHERE document.type=? AND last_action=?";
		$this->query($sql,$action_to,$flux,$action_from);
		
		$sql = "UPDATE document_action " .
				" JOIN document ON document_action.id_d=document.id_d " .
				" SET action=?" . 
				" WHERE document.type=? AND action=?";
		$this->query($sql,$action_to,$flux,$action_from);
		
	}
	
}