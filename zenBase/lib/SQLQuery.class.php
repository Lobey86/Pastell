<?php

require_once("ZLog.class.php");

class SQLQuery {
	
	private $dsn;
	private $user;
	private $password;
	
	private $pdo;
	private $lastPdoStatement;	
	private $nextResult;
	private $hasMoreResult;
	
	private $zLog;
	
	public function __construct($dsn,$user = null,$password = null){
		$this->dsn = $dsn;
		$this->user = $user;
		$this->password = $password;
		$this->setLog(new ZLog());
	}

	public function setLog(ZLog $zLog){
		$this->zLog = $zLog;
	}

	private function init(){
		if (! $this->pdo) {
			$this->pdo = new PDO($this->dsn,$this->user,$this->password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
			$this->logDebug("Connexion à la base de données ".$this->dsn);
			
		}
		$this->lastResult = false;
		$this->hasMoreResult = false;
	}
		
	public function query($query,$param = array()){
		$this->prepare($query);
		return $this->justExecute($param);
	}
	
	public function getLastError(){
		$error = $this->pdo->errorInfo();
		return implode(' ',$error);	
	}
		
	public function prepare($query){		
		$this->init();		
		try {
			$this->lastPdoStatement = $this->pdo->prepare($query);
		} catch (Exception $e){			
			$this->logEmerg("ERREUR DE SYNTAXE SUR LA REQUETE:  ".$e->getMessage()."|$query");
			throw $e;			
		}
	}
	
	public function prepareAndExecute($query,$param = array()){
		$this->prepare($query);
		$this->execute($param);
	}

	public function fetchOneValue($query,$param = array()){
		$data = $this->fetchOneLine($query,$param);
		if (! $data || count($data) < 1 ) {
			return false;	
		}
		$values = array_values($data);
		return $values[0];
	}
	
	public function fetchOneLine($query,$param = array()){
		$this->prepare($query);
		$this->execute($param);
		if (! $this->hasMoreResult()) {
			return false;	
		}
		return $this->fetch();
	}
	
	public function fetchAll($query,$param=array()){
		$result = array();
		$this->prepare($query);
		$this->execute($param);
		
		while ($this->hasMoreResult()) {
			$result[] = $this->fetch();
		}
		return $result;
	}
	
	public function execute($param = array()){
		$this->justExecute($param);
		$this->hasMoreResult = true;		
		$this->fetch();		
	}
	
	private function justExecute($param = array()){
		assert('$this->lastPdoStatement');
		$this->init();
		try {
			$result = $this->lastPdoStatement->execute($param);
			$this->logDebug($this->lastPdoStatement->queryString . "|" .implode(",",$param));			
		} catch (Exception $e){						
			$this->logEmerg("ECHEC DE LA REQUETE :  ".$e->getMessage()."|" . $this->lastPdoStatement->queryString . "|" .implode(",",$param) );
			throw $e;
		}
		return $result;
	}
	
	public function hasMoreResult(){
		return $this->hasMoreResult;
	}
	
	public function fetch(){
		
		$result = $this->nextResult; 
		$this->nextResult = $this->lastPdoStatement->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_NEXT);
		
		if (! $this->nextResult){
			$this->hasMoreResult = false;		
		}
		return $result;
	}
	
	private function logDebug($message){
		$this->zLog->log(__CLASS__." : $message",ZLog::DEBUG);
	}
	
	private function logEmerg($message){
		$this->zLog->log(__CLASS__." : $message",ZLog::EMERGENCY);		
	}	
}
