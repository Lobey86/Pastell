<?php
class UtilisateurListe extends SQL {
	
	const NB_UTILISATEUR_DISPLAY = 50;
	
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
	
	public function getNbUtilisateur($id_e,$with_fille = false,$role = false,$search = false){
		
		$sql = "SELECT count(DISTINCT utilisateur.id_u)" .				 
				" FROM utilisateur " .
				" JOIN utilisateur_role ON utilisateur.id_u=utilisateur_role.id_u ";
				
		if ($with_fille){
				$sql .= " JOIN entite_ancetre ON utilisateur_role.id_e=entite_ancetre.id_e " .
				" WHERE entite_ancetre.id_e_ancetre=?";
				$data = array($id_e);
		} else {
			$sql .= "WHERE utilisateur_role.id_e=?";
			$data = array($id_e);
		}	
		if ($role){
			$sql.= "AND utilisateur_role.role = ?";
			$data[] = $role;
		}
		if($search){
			$sql .= " AND nom LIKE ? OR prenom LIKE ? OR login LIKE ?";
			$data[]  = "%$search%";
			$data[]  = "%$search%";
			$data[]  = "%$search%";
		}
		
		return $this->queryOne($sql,$data);
	}
	
	public function getAllUtilisateur($id_e,$with_fille = false,$role = false,$search = false,$offset=0){
		$sql = "SELECT utilisateur.id_u,nom,prenom,login,utilisateur_role.role," .
				" email,utilisateur.id_e,entite.denomination ". 
				" FROM utilisateur " .
				" LEFT JOIN entite ON entite.id_e=utilisateur.id_e " .
				" JOIN utilisateur_role ON utilisateur.id_u=utilisateur_role.id_u ";
				
		if ($with_fille){
				$sql .= " JOIN entite_ancetre ON utilisateur_role.id_e=entite_ancetre.id_e " .
				" WHERE entite_ancetre.id_e_ancetre=?";
				$data = array($id_e);
		} else {
			$sql .= "WHERE utilisateur_role.id_e=?";
			$data = array($id_e);
		}	
		if ($role){
			$sql.= "AND utilisateur_role.role = ?";
			$data[] = $role;
		}
		if($search){
			$sql .= " AND nom LIKE ? OR prenom LIKE ? OR login LIKE ?";
			$data[]  = "%$search%";
			$data[]  = "%$search%";
			$data[]  = "%$search%";
		}
		
		$sql .= " GROUP BY utilisateur.id_u ";
		$sql .= " ORDER BY nom,prenom ";
		if ($offset != -1){
			$sql .= " LIMIT $offset,".self::NB_UTILISATEUR_DISPLAY;
		}
		
		$all= $this->query($sql,$data);
		
		$sql = "SELECT utilisateur_role.*,entite.denomination, role.libelle " .
				" FROM utilisateur_role " .
				" LEFT JOIN entite on utilisateur_role.id_e=entite.id_e " .
				" LEFT JOIN role ON utilisateur_role.role = role.role" .
				" WHERE id_u=? ";
		foreach($all as $i => $utilisateur){
			$all[$i]['all_role'] = $this->query($sql,$utilisateur['id_u']);
		}
		return $all;
	}

	
	
}