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
	
	public function __construct(SQLQuery $sqlQuery,RoleDroit $roleDroit){
		$this->sqlQuery = $sqlQuery;
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
	
	
	public function hasDroit($id_u,$droit,$id_e){
		$sql = "SELECT role FROM utilisateur_role WHERE id_u = ? AND id_e = ? ";
		$allRole = $this->sqlQuery->fetchAll($sql,$id_u,$id_e);
		foreach($allRole as $role){
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
		$sql = "SELECT utilisateur_role.*,denomination FROM utilisateur_role LEFT JOIN entite ON utilisateur_role.id_e=entite.id_e WHERE id_u = ?";
		return $this->sqlQuery->fetchAll($sql,$id_u);
	}
	
	public function hasOneDroit($id_u,$droit){
		$sql = "SELECT role FROM utilisateur_role WHERE id_u = ?";
		$allRole = $this->sqlQuery->fetchAll($sql,$id_u);
		foreach($allRole as $role){
			if ($this->roleDroit->hasDroit($role['role'],$droit)){
				return true;
			}
		}
		return false;
	
	}
	
	
	public function getEntite($id_u,$droit){
		$result = array();
		$sql = "SELECT role,id_e FROM utilisateur_role WHERE id_u = ? ";
		$allRole = $this->sqlQuery->fetchAll($sql,$id_u);
		foreach($allRole as $role){
			if ($this->roleDroit->hasDroit($role['role'],$droit)){
				$result[] = $role['id_e'];
			}
		}	
		
		$entiteListe = new EntiteListe($this->sqlQuery);
		$result = $entiteListe->getOnlyAncestor($result);
			
		return $result;
	}
	
}