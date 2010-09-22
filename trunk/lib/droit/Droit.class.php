<?php
//TODO deprecated

require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");


//L'utilisateur peut créer une collectivité
//L'utilisateur peut lire les transactions de la collectivité numéro 13

class Droit {
	
	const CREATE_SQL = "CREATE TABLE droit (id_u int NOT NULL, droit varchar(16) NOT NULL,type_objet varchar(16) NOT NULL, id_o varchar(16) NOT NULL)";
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function hasDroit($id_u,$droit,$type_objet,$id_o = 0){	
		$sql = "SELECT count(*)  FROM droit WHERE id_u = ? AND droit = ? AND type_objet = ? AND id_o = ?";
		if($this->sqlQuery->fetchOneValue($sql,$id_u,$droit,$type_objet,$id_o)) {
			return true;
		}
		$sql = "SELECT role FROM utilisateur_role WHERE id_u = ?";
		$allRole = $this->sqlQuery->fetchAll($sql,$id_u);
		foreach($allRole as $role){
				if (StandardRole::hasDroit($role['role'],$type_objet,$droit)){
					return true;
				}
		}
		return false;
		
	}
	
	public function addDroit($id_u, $droit, $type_objet,$id_o = 0){
		if ($this->hasDroit($id_u,$droit,$type_objet,$id_o)){
			return;
		}
		$sql = "INSERT INTO droit (id_u,droit,type_objet,id_o) VALUES (?,?,?,?)";
		$this->sqlQuery->query($sql,$id_u,$droit,$type_objet,$id_o);
	}
	
	public function removeDroit($id_u, $droit, $type_objet,$id_o = 0){
		$sql = "DELETE FROM droit WHERE  id_u=? AND droit=? AND type_objet = ?AND id_o=?";
		$this->sqlQuery->query($sql,$id_u,$droit,$type_objet,$id_o);
	}
	
	public function getAllDroit($id_u){
		$sql = "SELECT * FROM droit WHERE id_u = ?";
		return $this->sqlQuery->fetchAll($sql,$id_u);
	}
	
}