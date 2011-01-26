<?php


class DatabaseDefinition {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getDefinition(){
		
		$result = array();
		$tables = $this->sqlQuery->fetchAll('SHOW TABLE STATUS');
		foreach ($tables as $table) {
		
			$tableName = $table['Name'];
			$result[$tableName] = array (
				'Engine' => $table['Engine'],
				'Column' => $this->getColumnDefinition($tableName),
				'Index' =>  $this->getIndexDefinition($tableName),
			);
		}
		
		return $result;
	}
	
	private function getColumnDefinition($tableName){
		$r = array();
		$result = $this->sqlQuery->fetchAll("SHOW COLUMNS FROM $tableName");
		foreach($result as $line){
			$r[$line['Field']] = $line;
		}
		return $r;
	}
	
	private function getIndexDefinition($tableName){
		$result = array();
		$indexDefinition = array();
		$r = $this->sqlQuery->fetchAll("SHOW INDEX FROM $tableName");
		foreach ($r as $line){
			if (empty($result[$line['Key_name']])){
				$result[$line['Key_name']] = array('type'=>$line['Index_type'],
											'col'=>array(),
											'unique' => ! $line['Non_unique']);
			} 
			$result[$line['Key_name']]['col'][$line['Seq_in_index'] - 1] = $line['Column_name'];
		}
		
		return $result;
	}
}