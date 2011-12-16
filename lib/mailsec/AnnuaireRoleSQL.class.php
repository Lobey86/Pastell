<?php
class AnnuaireRoleSQL {
	
	private $sqlQuery;
	
	public function __construct($sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getAll($id_e){
		$sql = "SELECT * FROM annuaire_role WHERE id_e_owner=?";
		return $this->sqlQuery->fetchAll($sql,$id_e);
	}
	
	public function add($nom,$id_e_owner,$id_e,$role){
		$sql = "INSERT INTO annuaire_role(nom,id_e_owner,id_e,role) VALUES (?,?,?,?)";
		$this->sqlQuery->query($sql,$nom,$id_e_owner,$id_e,$role);
	}
	
	public function getUtilisateur($id_r){
		$sql = "SELECT * FROM annuaire_role WHERE id_r=?";
		$info = $this->sqlQuery->fetchOneLine($sql,$id_r);
		$roleUtilisateur = new RoleUtilisateur($this->sqlQuery);
		return $roleUtilisateur->getAllUtilisateurHerite($info['id_e'],$info['role']);
	}
	
	public function getInfo($id_r){
		$sql = "SELECT * FROM annuaire_role WHERE id_r=?";
		return $this->sqlQuery->fetchOneLine($sql,$id_r);
	}
	
	public function delete($id_r){
		$sql = "DELETE FROM annuaire_role WHERE id_r=?";
		$this->sqlQuery->query($sql,$id_r);
	}
	
	public function getList($id_e,$debut){
		$sql = "SELECT nom FROM annuaire_role WHERE id_e_owner=? AND nom LIKE ?";
		return $this->sqlQuery->fetchAll($sql,$id_e,"$debut%");
	}
	
	public function getFromNom($id_e,$nom){
		$sql = "SELECT id_r FROM annuaire_role WHERE id_e_owner=? AND nom=?";
		return $this->sqlQuery->fetchOneValue($sql,$id_e,$nom);
	}
	
}