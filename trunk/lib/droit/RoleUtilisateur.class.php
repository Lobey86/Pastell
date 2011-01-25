<?php

require_once("RoleDroit.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");

class RoleUtilisateur {
	
	const CREATE_SQL = "
CREATE TABLE utilisateur_role (
  id_u int(11) NOT NULL,
  role varchar(16) NOT NULL,
  id_e int NOT NULL
)";
	 
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
		$sql = "SELECT count(*) FROM utilisateur_role WHERE id_u=? AND role = ? AND id_e=? ";
		
		
		$sql = "SELECT count(*) FROM utilisateur_role WHERE id_u=? ";
		$nb_role= $this->sqlQuery->fetchOneValue($sql,$id_u);
		if ($nb_role == 1){
			$sql = "UPDATE utilisateur_role SET role='".RoleDroit::AUCUN_DROIT."' WHERE id_u = ? AND role = ? AND id_e = ?";
		} else {
			$sql = "DELETE FROM utilisateur_role WHERE id_u = ? AND role = ? AND id_e = ?";
		}
		$this->sqlQuery->query($sql,$id_u,$role,$id_e);
		
	}
	
	private function getAllRole($id_u,$id_e){
		static $allRole;
		if ( ! isset($allRole[$id_u][$id_e])){
			$sql = "SELECT role FROM utilisateur_role WHERE id_u = ? AND id_e = ? ";
			$allRole[$id_u][$id_e] = $this->sqlQuery->fetchAll($sql,$id_u,$id_e);
		}
		return $allRole[$id_u][$id_e];
	}
	
	
	
	
	public function hasDroit($id_u,$droit,$id_e){
	
		foreach($this->getAllRole($id_u,$id_e) as $role){
			if ($this->roleDroit->hasDroit($role['role'],$droit)){
				return true;
			}
		}
		if ($id_e == 0){
			return false;
		}
		$entite = new Entite($this->sqlQuery,$id_e);
		$id_e = $entite->getMere();
		if (! $id_e){
			$id_e = 0;
		}
		return $this->hasDroit($id_u,$droit,$id_e);
	}
	
	public function getRole($id_u){
		static $allRole;
		
		if (! isset($allRole[$id_u])){
			$sql = "SELECT utilisateur_role.*,denomination,siren,type FROM utilisateur_role LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e WHERE id_u = ?";
			$allRole[$id_u] = $this->sqlQuery->fetchAll($sql,$id_u);
		}
		return $allRole[$id_u]; 
	}
	
	private function getOneRole($id_u){
		static $allRole;
		if (! isset($allRole[$id_u])){
			$sql = "SELECT role FROM utilisateur_role WHERE id_u = ?";
			$allRole[$id_u] = $this->sqlQuery->fetchAll($sql,$id_u);
		}
		return $allRole[$id_u]; 
	}
	
	public function hasOneDroit($id_u,$droit){
		foreach($this->getOneRole($id_u) as $role){
			if ($this->roleDroit->hasDroit($role['role'],$droit)){
				return true;
			}
		}
		return false;
	}
	
	public function getEntiteWithDenomination($id_u,$droit){
		$result = array();
		$allRole = $this->getRole($id_u);
		foreach($allRole as $role){
			if ($this->roleDroit->hasDroit($role['role'],$droit)){
				$result[] = $role['id_e'];
				$mon_result[$role['id_e']] = $role;
			}
		}	
		$entiteListe = new EntiteListe($this->sqlQuery);
		$result = $entiteListe->getOnlyAncestor($result);
		$r = array();
		foreach($result as $id_e){
			$r[] = $mon_result[$id_e];
		}
		return $r;
	}
	
	public function getEntite($id_u,$droit){
		$result = array();
		$allRole = $this->getRole($id_u);
		foreach($allRole as $role){
			if ($this->roleDroit->hasDroit($role['role'],$droit)){
				$result[] = $role['id_e'];
			}
		}	
		$entiteListe = new EntiteListe($this->sqlQuery);
		$result = $entiteListe->getOnlyAncestor($result);
			
		return $result;
	}
	
	public function getDroit($id_u){
		$result = array();
		foreach($this->getRole($id_u) as $role){		
			$result =array_merge($result, $this->roleDroit->getDroit($role['role']));
		}
		return $result;
	}
}