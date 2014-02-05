<?php
abstract class SQL {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function query($query,$param = false){
		if ( ! is_array($param)){
			$param = func_get_args();
			array_shift($param);
    	}
		return $this->sqlQuery->query($query,$param);
	}
	
	public function queryOne($query,$param = false){
		if ( ! is_array($param)){
			$param = func_get_args();
			array_shift($param);
    	}
		return $this->sqlQuery->queryOne($query,$param);
	}
	
	public function queryOneCol($query,$param = false){
		if ( ! is_array($param)){
			$param = func_get_args();
			array_shift($param);
    	}
		return $this->sqlQuery->queryOneCol($query,$param);
	}
 
    public function lastInsertId($name = null) {
        return $this->sqlQuery->getPdo()->lastInsertId($name);
    }

}