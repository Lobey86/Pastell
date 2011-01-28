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
	
	public function getBySiren($siren,$offset,$search){
		$sql = "SELECT * FROM agent " . 
				" WHERE siren=? " . 
				" AND (nom_patronymique LIKE ? OR prenom LIKE ?) ".
				" ORDER BY nom_patronymique,prenom".
				" LIMIT $offset,".self::NB_MAX;
		return $this->sqlQuery->fetchAll($sql,$siren,"%$search%","%$search%");
	}
	
	public function getNbAgent($siren,$search){
		$sql = "SELECT count(*) FROM agent WHERE siren=? AND (nom_patronymique LIKE ? OR prenom LIKE ?)";
		return $this->sqlQuery->fetchOneValue($sql,$siren,"%$search%","%$search%");
	}
	
	public function getInfo($id_a,$siren){
		$sql = "SELECT * FROM agent " . 
			" WHERE id_a=? AND siren=? " ;
		return $this->sqlQuery->fetchOneLine($sql,$id_a,$siren);
	}
	
	
}