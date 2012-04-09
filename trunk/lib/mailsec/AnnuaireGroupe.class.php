<?php
class AnnuaireGroupe {
	
	const NB_MAX = 5;
	
	private $sqlQuery;
	private $id_e;
	
	
	public function __construct($sqlQuery,$id_e){
		$this->sqlQuery = $sqlQuery;
		$this->id_e = $id_e;
	}
	
	public function getInfo($id_g){
		$sql = "SELECT * FROM annuaire_groupe WHERE id_e=? AND id_g=?";
		return $this->sqlQuery->fetchOneLine($sql,$this->id_e,$id_g);
	}
	
	public function getGroupe(){
		$sql = "SELECT * FROM annuaire_groupe WHERE id_e=?";
		return $this->sqlQuery->fetchAll($sql,$this->id_e);
	}
	
	public function getFromNom($nom){
		$sql = "SELECT id_g FROM annuaire_groupe WHERE id_e=? AND nom=?";
		return $this->sqlQuery->fetchOneValue($sql,$this->id_e,$nom);
	}
	
	public function add($nom){
		$id_g = $this->getFromNom($nom);
		if ( ! $id_g){
			$sql = "INSERT INTO annuaire_groupe (id_e,nom) VALUES (?,?)";
			$this->sqlQuery->query($sql,$this->id_e,$nom);
		}
	}
	
	public function getNbUtilisateur($id_g){
		$sql = "SELECT count(*) FROM annuaire_groupe_contact WHERE id_g=?";
		return $this->sqlQuery->fetchOneValue($sql,$id_g);
	}
	
	public function getAllUtilisateur($id_g){
		$sql = "SELECT * FROM annuaire_groupe_contact " . 
		" JOIN annuaire ON annuaire_groupe_contact.id_a=annuaire.id_a " .
		" WHERE id_g=? ";
		return $this->sqlQuery->fetchAll($sql,$id_g);
	}
	
	public function getUtilisateur($id_g,$offset = 0){
		$sql = "SELECT * FROM annuaire_groupe_contact " . 
				" JOIN annuaire ON annuaire_groupe_contact.id_a=annuaire.id_a " .
				" WHERE id_g=? LIMIT $offset,".self::NB_MAX;
		return $this->sqlQuery->fetchAll($sql,$id_g);
	}
	
	public function delete(array $lesId_g){
		foreach ($lesId_g as $id_g){
			$sql = "DELETE FROM annuaire_groupe WHERE id_e = ? AND id_g=?";
			$this->sqlQuery->query($sql,$this->id_e,$id_g);
		}
	}
	
	public function isInGroupe($id_g,$id_a){
		$sql = "SELECT count(*) FROM annuaire_groupe_contact WHERE  id_g=? AND id_a=? ";
		return $this->sqlQuery->fetchOneValue($sql,$id_g,$id_a);
	}
	
	public function addToGroupe($id_g,$id_a){
		if ($this->isInGroupe($id_g,$id_a)){
			return;
		}
		$sql = "INSERT INTO annuaire_groupe_contact (id_g,id_a) VALUES (?,?)";
		$this->sqlQuery->query($sql,$id_g,$id_a);
	}
	
	public function deleteFromGroupe($id_g,array $id_aList){
		foreach ($id_aList as $id_a){
			$sql = "DELETE FROM annuaire_groupe_contact WHERE id_g=? AND id_a=?";
			$this->sqlQuery->query($sql,$id_g,$id_a);
		}
	}
	
	public function getListGroupe($debut){
		$sql = "SELECT nom FROM annuaire_groupe WHERE id_e=? AND nom LIKE ?";
		return $this->sqlQuery->fetchAll($sql,$this->id_e,"$debut%");
	}
	
}