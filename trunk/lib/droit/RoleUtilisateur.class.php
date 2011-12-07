<?php

require_once("RoleDroit.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");

class RoleUtilisateur {
	 
	private $sqlQuery;
	private $roleDroit;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
		$this->setRoleDroit(new RoleDroit());
	}
	
	public function setRoleDroit(RoleDroit $roleDroit){
		$this->roleDroit = $roleDroit;
	}
	
	public function addRole($id_u,$role,$id_e){
		$sql = "INSERT INTO utilisateur_role(id_u,role,id_e) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,$id_u,$role,$id_e);
	}
	
	public function removeRole($id_u,$role,$id_e) {		
		$sql = "SELECT count(*) FROM utilisateur_role WHERE id_u=? ";
		$nb_role= $this->sqlQuery->fetchOneValue($sql,$id_u);
		if ($nb_role == 1){
			$sql = "UPDATE utilisateur_role SET role='".RoleDroit::AUCUN_DROIT."' WHERE id_u = ? AND role = ? AND id_e = ?";
		} else {
			$sql = "DELETE FROM utilisateur_role WHERE id_u = ? AND role = ? AND id_e = ?";
		}
		$this->sqlQuery->query($sql,$id_u,$role,$id_e);
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
			foreach($this->sqlQuery->fetchAll($sql,$id_e,$id_u) as $line){
				$allDroit[$id_u."-".$id_e][] = $line['droit'];
			}
		}
		return $allDroit[$id_u."-".$id_e];
	}
	
	public function getRole($id_u){
		static $allRole;
		
		if (! isset($allRole[$id_u])){
			$sql = "SELECT utilisateur_role.*,denomination,siren,type FROM utilisateur_role LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e WHERE id_u = ?";
			$allRole[$id_u] = $this->sqlQuery->fetchAll($sql,$id_u);
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
			foreach($this->sqlQuery->fetchAll($sql,$id_u) as $line){
				$allDroit[$id_u][] = $line['droit']; 
			}
		}
		return $allDroit[$id_u]; 
	}
	
	public function hasOneDroit($id_u,$droit){
		$allDroit = $this->getAllDroit($id_u);
		return in_array($droit,$allDroit);
	}
	
	private function linearize($line,&$all,$profondeur){
		
		return $result;
	}
	
	private function linearizeTab($id_e,&$all,$profondeur){
		$result = array();
		if (empty($all[$id_e])){
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
		return $this->sqlQuery->fetchAll($sql,$id_u,$droit);
	}
	
	
	public function getArbreFille($id_u,$droit){
		$sql = "SELECT entite.id_e,entite.denomination,entite.entite_mere FROM entite_ancetre " .
				" JOIN utilisateur_role ON entite_ancetre.id_e_ancetre = utilisateur_role.id_e ".
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" JOIN entite ON entite_ancetre.id_e=entite.id_e ".
				" WHERE utilisateur_role.id_u=? AND droit=? ".
				" ORDER BY entite_mere,denomination";
				$result = array();
				
		foreach($this->sqlQuery->fetchAll($sql,$id_u,$droit) as $line){
			$result[$line['entite_mere']][] = array(
												'id_e' => $line['id_e'],
												'denomination' => $line['denomination'], 
												);
		}
		return $this->linearizeTab( 0,$result,0);
	}
	
	public function getEntiteWithDenomination($id_u,$droit){
		$sql = "SELECT utilisateur_role.*,denomination,siren,type " . 
				" FROM utilisateur_role " .
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e WHERE id_u = ?  AND droit=?";
		return $this->sqlQuery->fetchAll($sql,$id_u,$droit);
	}
	
	
	public function getEntite($id_u,$droit){
		$sql = "SELECT  DISTINCT utilisateur_role.id_e " . 
				" FROM utilisateur_role " .
				" JOIN role_droit ON utilisateur_role.role=role_droit.role ".
				" LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e " .
				" WHERE id_u = ?  AND droit=? ";
		$result = array();
		foreach($this->sqlQuery->fetchAll($sql,$id_u,$droit) as $line){
			$result[] = $line['id_e'];
		};
		return $result;
	}
	
	public function hasManyEntite($id_u,$role){
		if ($this->hasDroit($id_u,$role,0)){
			return true;
		}
		$sql = "SELECT count(distinct(id_e)) FROM utilisateur_role WHERE id_u = ?";
		
		$nb_entite = $this->sqlQuery->fetchOneValue($sql,$id_u);
		
		return ($nb_entite > 1);
	}
	
	
}