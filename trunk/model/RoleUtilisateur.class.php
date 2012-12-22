<?php

class RoleUtilisateur extends SQL {
	
	const AUCUN_DROIT = 'aucun droit';
	
	
	public function addRole($id_u,$role,$id_e){
		$sql = "INSERT INTO utilisateur_role(id_u,role,id_e) VALUES (?,?,?)";
		$this->query($sql,$id_u,$role,$id_e);
		if ($role != RoleUtilisateur::AUCUN_DROIT) {
			$sql = "DELETE FROM utilisateur_role WHERE id_u=? AND role=? AND id_e=?";
			$this->query($sql,$id_u,RoleUtilisateur::AUCUN_DROIT,$id_e);
		}
	}
	
	public function removeRole($id_u,$role,$id_e) {		
		$sql = "SELECT count(*) FROM utilisateur_role WHERE id_u=? ";
		$nb_role= $this->queryOne($sql,$id_u);
		if ($nb_role == 1){
			$sql = "UPDATE utilisateur_role SET role='".RoleUtilisateur::AUCUN_DROIT."' WHERE id_u = ? AND role = ? AND id_e = ?";
		} else {
			$sql = "DELETE FROM utilisateur_role WHERE id_u = ? AND role = ? AND id_e = ?";
		}
		$this->query($sql,$id_u,$role,$id_e);
	}
	
	public function hasDroit($id_u,$droit,$id_e){
		$allDroit = $this->getAllDroitEntite($id_u,$id_e);
		return in_array($droit,$allDroit);
	}
	
	public function getAllDroitEntite($id_u,$id_e){
		static $allDroit;
		
		if (! isset($allDroit[$id_u."-".$id_e])){
			$allDroit[$id_u."-".$id_e] = array();
			$sql = "SELECT droit FROM entite_ancetre " .
				" JOIN utilisateur_role ON entite_ancetre.id_e_ancetre = utilisateur_role.id_e ".
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" WHERE entite_ancetre.id_e=? AND utilisateur_role.id_u=? ";
			foreach($this->query($sql,$id_e,$id_u) as $line){
				$allDroit[$id_u."-".$id_e][] = $line['droit'];
			}
		}
		return $allDroit[$id_u."-".$id_e];
	}
	
	public function getRole($id_u){
		static $allRole;
		
		if (! isset($allRole[$id_u])){
			$sql = "SELECT utilisateur_role.*,denomination,siren,type FROM utilisateur_role LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e WHERE id_u = ?";
			$allRole[$id_u] = $this->query($sql,$id_u);
		}
		return $allRole[$id_u]; 
	}
	
	public function getAllDroit($id_u){
		static $allDroit;
		if (! isset($allDroit[$id_u])){
			$allDroit[$id_u] = array();
			$sql = "SELECT droit FROM  utilisateur_role ".
			" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
			" WHERE  utilisateur_role.id_u=? ";
			foreach($this->query($sql,$id_u) as $line){
				$allDroit[$id_u][] = $line['droit']; 
			}
		}
		return $allDroit[$id_u]; 
	}
	
	public function hasOneDroit($id_u,$droit){
		$allDroit = $this->getAllDroit($id_u);
		return in_array($droit,$allDroit);
	}
	
	private function linearizeTab($id_e,&$all,$profondeur){
		
		$result = array();
		if (empty($all[$id_e])){
			foreach($all as $id_e => $line){
				$result =array_merge($result,$this->linearizeTab($id_e,$all,0));	
			}
			return $result;
		}
		
		foreach($all[$id_e] as $line)  {
			$line['profondeur'] = $profondeur;
			$result[] = $line;
			if (isset($all[$line['id_e']])){
				$result = array_merge($result,$this->linearizeTab($line['id_e'],$all,$profondeur + 1));
			}
		}
		return $result;
	}
	
	public function getAllEntiteWithFille($id_u,$droit){
		$sql = "SELECT entite.id_e,entite.denomination,entite.siren,entite.centre_de_gestion,entite.entite_mere FROM entite_ancetre " .
				" JOIN utilisateur_role ON entite_ancetre.id_e_ancetre = utilisateur_role.id_e ".
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" JOIN entite ON entite_ancetre.id_e=entite.id_e ".
				" WHERE utilisateur_role.id_u=? AND droit=? ".
				" ORDER BY entite_mere,denomination";
		return $this->query($sql,$id_u,$droit);
	}
	
	
	public function getArbreFille($id_u,$droit){
		$sql = "SELECT entite.id_e,entite.denomination,entite.entite_mere FROM entite_ancetre " .
				" JOIN utilisateur_role ON entite_ancetre.id_e_ancetre = utilisateur_role.id_e ".
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" JOIN entite ON entite_ancetre.id_e=entite.id_e ".
				" WHERE utilisateur_role.id_u=? AND droit=? ".
				" ORDER BY entite_mere,denomination";
				$result = array();

		foreach($this->query($sql,$id_u,$droit) as $line){
			$result[$line['entite_mere']][] = array(
												'id_e' => $line['id_e'],
												'denomination' => $line['denomination'], 
												);
		}
		return $this->linearizeTab( 0,$result,0);
	}
	
	public function getEntiteWithDenomination($id_u,$droit){
		$sql = "SELECT DISTINCT entite.id_e,denomination,siren,type " . 
				" FROM utilisateur_role " .
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e WHERE id_u = ?  AND droit=?";
		return $this->query($sql,$id_u,$droit);
	}
	
	
	public function getEntite($id_u,$droit){
		$sql = "SELECT  DISTINCT utilisateur_role.id_e " . 
				" FROM utilisateur_role " .
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e " .
				" WHERE id_u = ?  AND droit=? ";
		$result = array();
		foreach($this->query($sql,$id_u,$droit) as $line){
			$result[] = $line['id_e'];
		};
		return $result;
	}
	
	public function hasManyEntite($id_u,$role){
		if ($this->hasDroit($id_u,$role,0)){
			return true;
		}
		$sql = "SELECT count(distinct(id_e)) FROM utilisateur_role WHERE id_u = ?";
		
		$nb_entite = $this->queryOne($sql,$id_u);
		return ($nb_entite > 1);
	}
	
	public function getAllUtilisateur($id_e,$role){
		$sql = "SELECT * FROM utilisateur_role " .
				" JOIN utilisateur ON utilisateur_role.id_u = utilisateur.id_u ".
				" WHERE utilisateur_role.id_e=? AND role =?";
		return $this->query($sql,$id_e,$role);
	}
	
	public function getAllUtilisateurHerite($id_e,$role){
		$sql = "SELECT * FROM entite_ancetre ".
				" JOIN utilisateur_role ON entite_ancetre.id_e=utilisateur_role.id_e ".
				" JOIN utilisateur ON utilisateur_role.id_u = utilisateur.id_u ".
				" WHERE entite_ancetre.id_e_ancetre=? AND role =?"
		;
		return $this->query($sql,$id_e,$role);
	}
	
	public function getAllUtilisateurWithDroit($id_e,$droit){
		$sql = "SELECT * FROM entite_ancetre ".
				" JOIN utilisateur_role ON entite_ancetre.id_e_ancetre=utilisateur_role.id_e ".
				" JOIN utilisateur ON utilisateur_role.id_u = utilisateur.id_u ".
				" JOIN role_droit ON role_droit.role = utilisateur_role.role " .
				" WHERE entite_ancetre.id_e=? AND droit =?"
		;
		return $this->query($sql,$id_e,$droit);
	}
	
}