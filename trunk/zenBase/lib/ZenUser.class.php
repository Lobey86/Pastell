<?php 
require_once("DatabaseDefinition.class.php");
require_once("SQLQuery.class.php");
require_once("ZenUserFile.class.php");
require_once("Date.class.php");

class ZenUser {
	
	private $zenUserFile;
	private $sqlQuery;
	private $anonymous;
	
	public function __construct(ZenUserFile $zenUserFile,SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
		$this->zenUserFile = $zenUserFile;
		$this->userName = $zenUserFile->getName();
	}
		
	public function setAnonymous(){
		$this->anonymous = true;
	}
	
	public function createFile(DatabaseDefinition $database,$password){
				
		foreach($database as $tableDefinition){
			$this->sqlQuery->query($tableDefinition);
		}	
		$this->sqlQuery->query("CREATE TABLE user(login,password,anonymous)");
		$this->sqlQuery->query("INSERT INTO user(login,password,anonymous) VALUES (?,?,?)",array($this->userName,md5($password),$this->anonymous));
		$this->sqlQuery->query("CREATE TABLE temporary_password (password,date datetime)");
	}

	public function isPasswordOK($password){
		return $this->sqlQuery->fetchOneValue("SELECT count(*) FROM user WHERE login=? AND password=?",array($this->userName,md5($password)));
	}
	
	public function connectionOK($password){
		if (! $this->zenUserFile->fileExists()){
			return false;
		}
		return $this->isPasswordOK($password);
	}
	
	public function getName(){
		if ($this->anonymous){
			return "anonyme";
		}
		return $this->userName;
	}
	
	public function changePassword($password){
		$this->sqlQuery->query("UPDATE user SET password=?",array(md5($password)));
	}

	public function getMD5Password(){
		return $this->sqlQuery->fetchOneValue("SELECT password FROM user WHERE login=?",array($this->userName));		
	}
	
	public function changeUserName($login){
		$this->sqlQuery->query("UPDATE user SET login=? WHERE login=?",array($login,$this->userName));
		$this->userName = $login;
	}
	
	public function setTemporaryPassword($temp_pass){
		$this->sqlQuery->query("DELETE FROM temporary_password");
		$this->sqlQuery->query("INSERT INTO temporary_password(password,date) VALUES (?,?)",array($temp_pass,date(Date::DATE_ISO)));
	}
	
	public function isTempPasswordOk($temp_pass){
		return $this->sqlQuery->fetchOneValue("SELECT count(*) FROM temporary_password WHERE password=? AND date > ?",
					array($temp_pass,date(Date::DATE_ISO,strtotime("- 1 hour"))));	
	}
	
}