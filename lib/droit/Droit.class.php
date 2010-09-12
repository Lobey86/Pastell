<?php
require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");

class Droit {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function hasDroit($id_u,$droit,$id_objet = 0){
		$sql = "SELECT * FROM droit WHERE id_u=? AND droit=? AND id_objet=?";
		return $this->sqlQuery->fetchOneValue($sql,$id_u,$droit,$id_objet);
	}
	
	public function addDroit($id_u, $droit, $id_objet = 0){
		$sql = "INSERT INTO droit (id_u,droit,id_objet) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,$id_u,$droit,$id_objet);
	}
	
	public function removeDroit($id_u, $droit, $id_objet = 0){
		$sql = "DELETE FROM droit WHERE  id_u=? AND droit=? AND id_objet=?";
		$this->sqlQuery->query($sql,$id_u,$droit,$id_objet);
	}
	
	
}