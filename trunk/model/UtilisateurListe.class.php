<?php
class UtilisateurListe extends SQL {
	
	public function getNbUtilisateur($search = false){
		$sql = "SELECT count(*) FROM utilisateur ";
		$data = array();
		if ($search){
			$sql .= " WHERE nom LIKE ? OR prenom LIKE ? OR login LIKE ?";
			$data  = array("%$search%","%$search%","%$search%");
		}
		return $this->queryOne($sql,$data);
	}
	
	public function getAll($offset,$limit,$search = false){
		$sql = "SELECT * FROM utilisateur LEFT JOIN entite ON utilisateur.id_e=entite.id_e";
				
		$data = array();
		if ($search){
			$sql .= " WHERE nom LIKE ? OR prenom LIKE ? OR login LIKE ?";
			$data  = array("%$search%","%$search%","%$search%");
		}
		$sql .= " ORDER BY utilisateur.nom,prenom,login LIMIT $offset,$limit";
		
		$result =  $this->query($sql,$data);
		return $result;
	}
	
	public function getUtilisateurByLogin($login){
		$sql = "SELECT id_u FROM utilisateur WHERE login = ?";
		return $this->queryOne($sql,$login);
	}
	
	public function getUtilisateurByCertificat($verif_number,$offset,$limit){
		$sql = "SELECT * FROM utilisateur" .
				" WHERE certificat_verif_number = ? " .
				" ORDER BY utilisateur.nom,prenom,login LIMIT $offset,$limit";
		return $this->query($sql,$verif_number);
	}
	
	public function getNbUtilisateurByCertificat($verif_number){
		$sql = "SELECT count(*) FROM utilisateur WHERE certificat_verif_number=?";
		return $this->queryOne($sql,$verif_number);
	}
	
	public function getByLoginOrEmail($login,$email){
		$sql = "SELECT id_u FROM utilisateur WHERE (login = ? OR email=?) AND mail_verifie=1";
		return $this->queryOne($sql,$login,$email);
	}
	
	public function getByVerifPassword($mail_verif_password){
		$sql = "SELECT id_u FROM utilisateur WHERE mail_verif_password = ?  AND mail_verifie=1";
		return $this->queryOne($sql,$mail_verif_password);
	}
	
	public function getUtilisateurByEntite(array $id_e){
		$all_id_e = implode(',',$id_e);
		$sql = "SELECT * FROM  utilisateur " . 
				" LEFT JOIN utilisateur_role  ON utilisateur_role.id_u = utilisateur.id_u ".
				" LEFT JOIN entite ON utilisateur.id_e = entite.id_e " .
				" WHERE utilisateur_role.id_e IN ($all_id_e) " . 
				" ORDER BY utilisateur.nom,utilisateur.prenom";
		$all= $this->query($sql);
		
		$result = array();
		foreach($all as $ligne){	
			if (empty($result[$ligne['id_u']])){
				$result[$ligne['id_u']] = $ligne;
			}
			$result[$ligne['id_u']]['all_role'][] = $ligne['role'];			
		}
		return $result;
	}
	
	
	public function getUtilisateurByEntiteAndDroit(array $id_e,$droit){
		$all_id_e = implode(',',$id_e);
		$sql = "SELECT * FROM utilisateur_role " . 
				" JOIN utilisateur ON utilisateur_role.id_u = utilisateur.id_u ".
				" LEFT JOIN entite ON utilisateur.id_e = entite.id_e ".
				" JOIN role_droit ON utilisateur_role.role=role_droit.role " .
				" WHERE utilisateur_role.id_e IN ($all_id_e) " . 
				" AND role_droit.droit= ? " .
				" ORDER BY utilisateur.nom,utilisateur.prenom";
		$all= $this->query($sql,$droit);
		
		$result = array();
		foreach($all as $ligne){	
			if (empty($result[$ligne['id_u']])){
				$result[$ligne['id_u']] = $ligne;
			}
			$result[$ligne['id_u']]['all_role'][] = $ligne['role'];			
		}
		return $result;
	}
	
}