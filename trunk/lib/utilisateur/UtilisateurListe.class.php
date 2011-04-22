<?php

require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");

class UtilisateurListe {
	
	private $sqlQuery;
	
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getNbUtilisateur($search = false){
		$sql = "SELECT count(*) FROM utilisateur ";
		$data = array();
		if ($search){
			$sql .= " WHERE nom LIKE ? OR prenom LIKE ? OR login LIKE ?";
			$data  = array("%$search%","%$search%","%$search%");
		}
		return $this->sqlQuery->fetchOneValue($sql,$data);
	}
	
	public function getAll($offset,$limit,$search = false){
		$sql = "SELECT * FROM utilisateur LEFT JOIN entite ON utilisateur.id_e=entite.id_e";
				
		$data = array();
		if ($search){
			$sql .= " WHERE nom LIKE ? OR prenom LIKE ? OR login LIKE ?";
			$data  = array("%$search%","%$search%","%$search%");
		}
		$sql .= " ORDER BY utilisateur.nom,prenom,login LIMIT $offset,$limit";
		
		$result =  $this->sqlQuery->fetchAll($sql,$data);
	
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
		$all= $this->sqlQuery->fetchAll($sql,$id_e);
		
		$result = array();
		foreach($all as $ligne){	
			if (empty($result[$ligne['id_u']])){
				$result[$ligne['id_u']] = $ligne;
			}
			$result[$ligne['id_u']]['all_role'][] = $ligne['role'];			
		}
		return $result;
	}
	
	public function getUtilisateurByCertificat($verif_number,$offset,$limit){
		$sql = "SELECT * FROM utilisateur" .
				" WHERE certificat_verif_number = ? " .
				" ORDER BY utilisateur.nom,prenom,login LIMIT $offset,$limit";
		return $this->sqlQuery->fetchAll($sql,$verif_number);
	}
	
	public function getNbUtilisateurByCertificat($verif_number){
		$sql = "SELECT count(*) FROM utilisateur WHERE certificat_verif_number=?";
		return $this->sqlQuery->fetchOneValue($sql,$verif_number);
	}
	
	public function getByLoginOrEmail($login,$email){
		$sql = "SELECT id_u FROM utilisateur WHERE (login = ? OR email=?) AND mail_verifie=1";
		return $this->sqlQuery->fetchOneValue($sql,$login,$email);
	}
	
	public function getByVerifPassword($mail_verif_password){
		$sql = "SELECT id_u FROM utilisateur WHERE mail_verif_password = ?  AND mail_verifie=1";
		return $this->sqlQuery->fetchOneValue($sql,$mail_verif_password);
	}
	
}