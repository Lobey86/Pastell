<?php

class RoleSQL {
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getAllRole(){
		$sql = "SELECT * FROM role ORDER by libelle";
		return $this->sqlQuery->fetchAll($sql);
	}
	
	public function edit($role,$libelle){
		$sql = "SELECT count(*) FROM role WHERE role=?";
		if ($this->sqlQuery->fetchOneValue($sql,$role)){
			$sql = "UPDATE role SET libelle = ? WHERE role = ? ";
			$this->sqlQuery->query($sql,$libelle,$role);
		} else {
			$sql = "INSERT INTO role (role,libelle) VALUES (?,?)";
			$this->sqlQuery->query($sql,$role,$libelle);
		}
	}
	
	public function getDroit(array $allDroit,$role){
		$result = array_fill_keys($allDroit,false);
		$sql = "SELECT * FROM role_droit WHERE role=?";
		foreach($this->sqlQuery->fetchAll($sql,$role) as $line){
			$result[$line['droit']] = true;
		}
		return $result;
	}
	
	public function updateDroit($role,array $lesDroits){
		$sql = "DELETE FROM role_droit WHERE role=?";
		$this->sqlQuery->query($sql,$role);
		foreach($lesDroits as $droit){
			$sql="INSERT INTO role_droit(role,droit) VALUES (?,?)";
			$this->sqlQuery->query($sql,$role,$droit);
		}
	}
	
}