<?php


//TODO pas bon ?
class DocumentActionList {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	
	public function getFromAction($action){
		$sql = "SELECT * FROM document WHERE last_action=?"; 
			
		return $this->sqlQuery->fetchAll($sql,$action);
	}
	
}