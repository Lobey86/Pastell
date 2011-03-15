<?php

class AgentSQL {
	
	const NB_MAX = 20;
	
	public function __construct($sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	//"Matricule (5)";"Titre";"Nom d'usage";"Nom patronymique";"Prénom";"Emploi / Grade (C)";
	//"Emploi / Grade (L)";"Collectivité (C)";"Collectivité (L)";"SIREN";"Type de dossier";"Type de dossier (L)"
	//;"Train de traitement (C)";"Train de traitement (L)"
	public function add($info,$infoCollectivite = array()){
		
		if ($infoCollectivite){
			$info[9] = $infoCollectivite['siren'];
		}
		
		$sql = "DELETE FROM agent WHERE siren=? AND matricule=?";
		$this->sqlQuery->query($sql,$info[9],$info[0]);
		
		$sql = "INSERT INTO agent (matricule,titre,nom_usage,nom_patronymique,prenom,emploi_grade_code,emploi_grade_libelle,collectivite_code,collectivite_libelle ,siren,type_dossier_code ,type_dossier_libelle,train_traitement_code,train_traitement_libelle)" . 
				" VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->sqlQuery->query($sql,$info);
		return true;
	}
	
	public function getBySiren($siren,$offset,$search = ''){
		$sql = "SELECT * FROM agent " . 
				" WHERE siren=? " . 
				" AND (nom_patronymique LIKE ? OR prenom LIKE ?) ".
				" ORDER BY nom_patronymique,prenom".
				" LIMIT $offset,".self::NB_MAX;
		return $this->sqlQuery->fetchAll($sql,$siren,"%$search%","%$search%");
	}
	
	public function getNbAgent($siren,$search = ''){
		$sql = "SELECT count(*) FROM agent WHERE siren=? AND (nom_patronymique LIKE ? OR prenom LIKE ?)";
		return $this->sqlQuery->fetchOneValue($sql,$siren,"%$search%","%$search%");
	}
	
	public function getInfo($id_a,$siren){
		$sql = "SELECT * FROM agent " . 
			" WHERE id_a=? AND siren=? " ;
		return $this->sqlQuery->fetchOneLine($sql,$id_a,$siren);
	}
	
	public function getNbAllAgent($search){
		$sql = "SELECT  count(*) FROM agent " . 
		" JOIN entite ON entite.siren=agent.siren " . 
		" JOIN entite_ancetre ON entite.id_e=entite_ancetre.id_e " .
		" JOIN utilisateur_role ON entite_ancetre.id_e_ancetre = utilisateur_role.id_e " . 
		" JOIN role_droit ON utilisateur_role.role=role_droit.role " .
		" WHERE droit='entite:lecture' AND utilisateur_role.id_u=1 AND (nom_patronymique LIKE ? OR prenom LIKE ?)";
		return  $this->sqlQuery->fetchOneValue($sql,"%$search%","%$search%");
	}
	
	public function getAllAgent($search,$offset){
		$sql = "SELECT agent.*,entite.id_e,entite.denomination FROM agent " . 
		" JOIN entite ON entite.siren=agent.siren " . 
		" JOIN entite_ancetre ON entite.id_e=entite_ancetre.id_e " .
		" JOIN utilisateur_role ON entite_ancetre.id_e_ancetre = utilisateur_role.id_e " . 
		" JOIN role_droit ON utilisateur_role.role=role_droit.role " .
		" WHERE droit='entite:lecture' AND utilisateur_role.id_u=1 AND (nom_patronymique LIKE ? OR prenom LIKE ?)" .
		" ORDER BY nom_patronymique,prenom".
		" LIMIT $offset,".self::NB_MAX;;
		return  $this->sqlQuery->fetchAll($sql,"%$search%","%$search%");
	}
	
	public function clean($siren){
		$sql =  "DELETE FROM agent WHERE siren = ?";
		$this->sqlQuery->query($sql,$siren);
	}
	
}