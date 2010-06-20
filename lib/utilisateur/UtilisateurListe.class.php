<?php

require_once( ZEN_PATH . "/lib/SQLQuery.class.php");

class UtilisateurListe {
	
	private $sqlQuery;
	
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getAll(){
		$sql = "SELECT utilisateur.*,entite.* FROM utilisateur " . 
				" LEFT JOIN utilisateur_role ON utilisateur.id_u = utilisateur_role.id_u " . 
				" LEFT JOIN entite ON utilisateur_role.siren = entite.siren ".
				" ORDER BY utilisateur.nom,prenom,login";
		return $this->sqlQuery->fetchAll($sql);
	}
	
	public function getUtilisateurByLogin($login){
		$sql = "SELECT id_u FROM utilisateur WHERE login = ?";
		return $this->sqlQuery->fetchOneValue($sql,array($login));
	}
	
	public function getUtilisateurByEntite($siren){
		$sql = "SELECT * FROM utilisateur_role " . 
				" JOIN utilisateur ON utilisateur_role.id_u = utilisateur.id_u ".
				" WHERE utilisateur_role.siren = ? " . 
				" ORDER BY utilisateur.nom,utilisateur.prenom";
		return $this->sqlQuery->fetchAll($sql,array($siren));
	}
	
}