<?php

class Notification extends SQL {
	
	public function add($id_u,$id_e,$type,$action){
		$sql = "SELECT count(*) FROM notification WHERE id_u = ? AND id_e=? AND type=? AND action=?";
		$nb = $this->queryOne($sql,$id_u,$id_e,$type,$action);
		if ($nb){
			return;
		}
		$sql = "INSERT INTO notification(id_u,id_e,type,action) VALUES (?,?,?,?)";
		$this->query($sql,$id_u,$id_e,$type,$action);
	}
	
	public function getAll($id_u){
		$sql = "SELECT notification.*,entite.denomination FROM notification LEFT JOIN entite ON notification.id_e = entite.id_e WHERE id_u=?";
		return $this->query($sql,$id_u);
	}
	
	public function getInfo($id_n){
		return $this->queryOne("SELECT * FROM notification WHERE id_n=?",$id_n);
	}
	
	public function remove($id_n){
		return $this->query("DELETE FROM notification WHERE id_n=?",$id_n);
	}
	
	public function getMail($id_e,$type,$action){
		$result = array();
		
		$sql = "SELECT * FROM notification " . 
				" JOIN utilisateur ON notification.id_u = utilisateur.id_u " . 
				" WHERE (notification.id_e=? OR notification.id_e=0) AND (type=? OR type='0') AND (action=? OR action='0')";
		$resultSQL = $this->query($sql,$id_e,$type,$action);
		
		foreach($resultSQL as $ligne){
			$result[] = $ligne['email'];
		}
		return $result;
	}
	
}