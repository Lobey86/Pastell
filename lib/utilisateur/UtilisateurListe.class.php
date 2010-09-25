<?php

require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");

class UtilisateurListe {
	
	private $sqlQuery;
	
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getNbUtilisateur(){
		$sql = "SELECT count(*) FROM utilisateur ";
		return $this->sqlQuery->fetchOneValue($sql);
	}
	
	public function getAll($offset,$limit){
		$sql = "SELECT * FROM utilisateur" .
				" ORDER BY utilisateur.nom,prenom,login LIMIT $offset,$limit";
		$result =  $this->sqlQuery->fetchAll($sql);
	
		return $result;
	}
	
	public function getUtilisateurByLogin($login){
		$sql = "SELECT id_u FROM utilisateur WHERE login = ?";
		return $this->sqlQuery->fetchOneValue($sql,$login);
	}
	
	public function getUtilisateurByEntite($id_e){
		$sql = "SELECT * FROM utilisateur_role " . 
				" JOIN utilisateur ON utilisateur_role.id_u = utilisateur.id_u ".
				" WHERE utilisateur_role.id_e = ? " . 
				" ORDER BY utilisateur.nom,utilisateur.prenom";
		return $this->sqlQuery->fetchAll($sql,$id_e);
	}
	
}