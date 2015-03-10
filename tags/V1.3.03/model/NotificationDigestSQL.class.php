<?php
class NotificationDigestSQL extends SQL {
	
	public function add($mail,$id_e,$id_d,$action,$type,$message){
		$sql = "INSERT INTO notification_digest(mail,id_e,id_d,action,type,message) VALUES (?,?,?,?,?,?)";
		$this->query($sql,$mail,$id_e,$id_d,$action,$type,$message);
	}
	
	public function getAll(){
		$sql = "SELECT * FROM notification_digest";
		$result = array();
		foreach($this->query($sql) as $info){
			$result[$info['mail']][] = $info;
		}
		return $result;
		
	}
	
	public function delete($id_nd){
		$sql = "DELETE FROM notification_digest WHERE id_nd = ?";
		$this->query($sql,$id_nd);
	}
	
}