<?php

class Notification extends SQL {
	
	const ALL_TYPE = "0";
	
	public function add($id_u,$id_e,$type,$action,$daily_digest){
		$sql = "SELECT count(*) FROM notification WHERE id_u = ? AND id_e=? AND type=? AND action=? AND daily_digest=?";
		$nb = $this->queryOne($sql,$id_u,$id_e,$type,$action,$daily_digest);
		if ($nb){
			return;
		}
		$sql = "INSERT INTO notification(id_u,id_e,type,action,daily_digest) VALUES (?,?,?,?,?)";
		$this->query($sql,$id_u,$id_e,$type,$action,$daily_digest);
	}
	
	public function getAll($id_u){
		$sql = "SELECT notification.*,entite.denomination FROM notification LEFT JOIN entite ON notification.id_e = entite.id_e WHERE id_u=?";
		$result = array();
		foreach($this->query($sql,$id_u) as $line){
			if (empty($result[$line['id_e']."-".$line['type']])){
				$result[$line['id_e']."-".$line['type']] = $line;
				$result[$line['id_e']."-".$line['type']]['action'] = array();
			}
			$result[$line['id_e']."-".$line['type']]['action'][] = $line['action'];
		}
		return $result;
	}
	
	public function hasDailyDigest($id_u,$id_e,$type){
		$sql = "SELECT daily_digest FROM notification WHERE id_u=? AND id_e=? AND type=? LIMIT 1";
		return $this->queryOne($sql,$id_u,$id_e,$type);
	}
	
	public function getNotificationActionList($id_u,$id_e,$type,$action_list){
		$sql = "SELECT * FROM notification WHERE id_u=? AND id_e=? AND type=?";
		$result = array();
		foreach($this->query($sql,$id_u,$id_e,$type) as $line){
			$result[$line['action']] = 1;
		}
		
		foreach($action_list as $i => $action){
			$action_list[$i]['checked'] = isset($result[$action['id']]) || isset($result[0]);
		}
		
		return $action_list;
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
	
	public function getAllInfo($id_e,$type,$action){
		$sql = "SELECT * FROM notification " .
				" JOIN utilisateur ON notification.id_u = utilisateur.id_u " .
				" WHERE (notification.id_e=? OR notification.id_e=0) AND (type=? OR type='0') AND (action=? OR action='0')";
		return $this->query($sql,$id_e,$type,$action);
	}
	
	public function removeAll($id_u,$id_e,$type){
		$sql = "DELETE FROM notification WHERE id_u=? AND id_e=? AND type=?";
		$this->query($sql,$id_u,$id_e,$type);
	}
	
	public function toogleDailyDigest($id_u,$id_e,$type){
		$sql = "UPDATE notification SET daily_digest = 1 - daily_digest WHERE id_u=? AND id_e=? AND type=?";
		$this->query($sql,$id_u,$id_e,$type);
	}
	
	
}