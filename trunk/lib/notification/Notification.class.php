<?php

class Notification {
	
	public function __construct(sqlQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function add($id_u,$id_e,$type,$action){
		$sql = "SELECT count(*) FROM notification WHERE id_u = ? AND id_e=? AND type=? AND action=?";
		$nb = $this->sqlQuery->fetchOneValue($sql,$id_u,$id_e,$type,$action);
		if ($nb){
			return;
		}
		$sql = "INSERT INTO notification(id_u,id_e,type,action) VALUES (?,?,?,?)";
		$this->sqlQuery->query($sql,$id_u,$id_e,$type,$action);
	}
	
	public function getAll($id_u){
		$sql = "SELECT notification.*,entite.denomination FROM notification LEFT JOIN entite ON notification.id_e = entite.id_e WHERE id_u=?";
		return $this->sqlQuery->fetchAll($sql,$id_u);
	}
	
	public function getInfo($id_n){
		return $this->sqlQuery->fetchOneLine("SELECT * FROM notification WHERE id_n=?",$id_n);
	}
	
	public function remove($id_n){
		return $this->sqlQuery->query("DELETE FROM notification WHERE id_n=?",$id_n);
	}
	
	public function getMail($id_d,$action){
		return array("eric@babette.com");
	}
	
}