<?php

class Document extends SQL {
	
	const MAX_ESSAI = 5;
	
	private $passwordGenerator;
	
	public function __construct(SQLQuery $sqlQuery,PasswordGenerator $passwordGenerator){
		parent::__construct($sqlQuery);
		$this->passwordGenerator = $passwordGenerator;
	}

	public function getNewId(){
		for ($i=0; $i<self::MAX_ESSAI; $i++){
			$id_d = $this->passwordGenerator->getPassword();
			$sql = "SELECT count(*) FROM document WHERE id_d=?";
			$nb = $this->queryOne($sql,$id_d);
			
			if ($nb == 0){
				return $id_d;
			}	
		}
		throw new Exception("Impossible de trouver un numéro de transaction");
	}
	
	public function save($id_d,$type){
		$sql = "INSERT INTO document(id_d,type,creation,modification) VALUES (?,?,now(),now())";
		$this->query($sql,$id_d,$type);
	}
	
	public function setTitre($id_d,$titre){
		$sql = "UPDATE document SET titre = ?,modification=now() WHERE id_d = ?";
		$this->query($sql,$titre,$id_d);
	}
	
	public function getInfo($id_d){
		$sql = "SELECT * FROM document WHERE id_d = ? ";
		return $this->queryOne($sql,$id_d);
	}
	
	public function getIdFromTitre($titre,$type){		
		$sql = "SELECT id_d FROM document WHERE titre=? AND type=?";
		return $this->queryOne($sql,$titre,$type);
	}
	
	public function getIdFromEntiteAndTitre($id_e,$titre,$type){
		$sql = "SELECT document.id_d FROM document " .
				" JOIN document_entite ON document.id_d=document_entite.id_d ".
				" WHERE id_e=? AND titre=? AND type=?";
		return $this->queryOne($sql,$id_e,$titre,$type);
	}
	
	public function delete($id_d){
		$sql = "DELETE FROM document WHERE id_d=?";
		$this->query($sql,$id_d);
		
		$sql = "SELECT id_a FROM document_action WHERE id_d=?";
		$id_a = $this->queryOne($sql,$id_d);
		
		$sql = "DELETE FROM document_action_entite WHERE id_a=?";	
		$this->query($sql,$id_a);
		
		$sql = "DELETE FROM document_action WHERE id_d=?";
		$this->query($sql,$id_d);
		$sql = "DELETE FROM document_entite WHERE id_d=?";
		$this->query($sql,$id_d);
		
		$sql = "DELETE FROM document_index WHERE id_d=?";
		$this->query($sql,$id_d);
	}
	
	public function getAllByType($type){
		$sql = "SELECT id_d,titre FROM document WHERE type=?";
		return $this->query($sql,$type);
	}
	
	public function fixModule($old_flux_name,$new_flux_name){
		$sql = "UPDATE document SET type= ? WHERE type = ?";
		return $this->query($sql,$new_flux_name,$old_flux_name);
	}
	
	
}