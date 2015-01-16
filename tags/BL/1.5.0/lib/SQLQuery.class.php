<?php
class SQLQuery {
	
	const DATABASE_TYPE = "mysql";
	const DEFAULT_HOST = "localhost";
	const SLOW_QUERY_IN_MS = 2000;
	
	private $dsn;
	private $user;
	private $password;
	
	private $pdo;
	
	public function __construct($bd_dsn,$bd_user = null,$bd_password = null){
		$this->dsn = $bd_dsn;
		$this->user = $bd_user;
		$this->password = $bd_password;
	}
	
	public function disconnect(){
		$this->pdo = null;
	}
	
	public function getPdo(){
		if ( ! $this->pdo){
			
			$this->pdo = new PDO($this->dsn,$this->user,$this->password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
		}
		return $this->pdo;
	}
	
	public function query($query,$param = false){
		$start = microtime(true);
		if ( ! is_array($param)){
			$param = func_get_args();
			array_shift($param);
    	}
		
    	try {
    		$pdoStatement = $this->getPdo()->prepare($query);
    	} catch (Exception $e) {	
    		throw new Exception($e->getMessage() . " - " .$query);
		}	
		
		try {
			$pdoStatement->execute($param);
		} catch (Exception $e) {
			
			throw new Exception( $e->getMessage() ." - ". $pdoStatement->queryString . "|" .implode(",",$param));	
		}
		$result = array();
		if ($pdoStatement->columnCount()){
			$result = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
		} 
		
		$duration = microtime(true) - $start;
		if ($duration > self::SLOW_QUERY_IN_MS ){
			$requete =  $pdoStatement->queryString . "|" .implode(",",$param);
			trigger_error("Requete lente ({$duration}ms): $requete",E_USER_WARNING);
		}
		 
		return $result;
	}
	
	public function queryOne($query,$param = false){
		if ( ! is_array($param)){
			$param = func_get_args();
			array_shift($param);
    	}
		$result = $this->query($query,$param);
		if (! $result){
			return false;
		}
		
		$result = $result[0];
		if (count($result) == 1){
			return reset($result);
		}
		return $result;
	}
	
	public function queryOneCol($query,$param = false){
		if ( ! is_array($param)){
			$param = func_get_args();
			array_shift($param);
    	}
    	$result = $this->query($query,$param);
		if (! $result){
			return array();
		}
		$r = array();
		foreach($result as $line){
			$line = array_values($line);
			$r[] = $line[0];
		}
		return $r;
	}
	
	private $lastPdoStatement;	
	private $nextResult;
	private $hasMoreResult;
	
	public function prepareAndExecute($query,$param = false){
		if ( ! is_array($param)){
			$param = func_get_args();
			array_shift($param);
    	}
		$this->lastPdoStatement = $this->getPdo()->prepare($query);
		$this->lastPdoStatement->execute($param);		
		$this->hasMoreResult = true;		
		$this->fetch();		
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
}
