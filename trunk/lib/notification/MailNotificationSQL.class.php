<?php

class MailNotificationSQL {
	
	public function __construct(sqlQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function addNotification($siren,$email,$type = "default"){
		$sql = "SELECT count(*) FROM mail_notification WHERE siren=? AND mail=? AND type=?";
		$nb = $this->sqlQuery->fetchOneValue($sql,array($siren,$email,$type));
		if ($nb){
			return;
		}
		$sql = "INSERT INTO mail_notification(siren,mail,type) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,array($siren,$email,$type));
	}
		
}