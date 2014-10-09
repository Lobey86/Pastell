<?php

require_once("DatabaseDefinition.interface.php");

class DatabaseDefinitionPostgreSQL implements DatabaseDefinition {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getDefinition(){
		
		$result = array();
		
		$sql = "SELECT table_name " .
				" FROM information_schema.tables " .
				" WHERE table_type = 'BASE TABLE' " .
				" AND table_schema NOT IN ('pg_catalog', 'information_schema');";
		
		
		$tables = $this->sqlQuery->query($sql);
		
		foreach ($tables as $table) {
			$tableName = $table['table_name'];
			$result[$tableName] = array (
				'Column' => $this->getColumnDefinition($tableName),
				'Index' =>  $this->getIndexDefinition($tableName),
				'Constaints' => $this->getConstraints($tableName),
			);
		}
		$sql = "SELECT relname 
  FROM pg_class
 WHERE relkind = 'S'
   AND relnamespace IN (
        SELECT oid
          FROM pg_namespace
         WHERE nspname NOT LIKE 'pg_%'
           AND nspname != 'information_schema'
);";
		
		$sequences = $this->sqlQuery->query($sql);
		$result['sequence'] = $sequences;
		
		return $result;
	}
	
	private function getColumnDefinition($tableName){
		$r = array();
		$result = $this->sqlQuery->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME= ?;",$tableName);
		foreach($result as $line){
			$r[$line['column_name']] = $line;
		}
		return $r;
	}
	
	private function getIndexDefinition($tableName){
		$result = array();
		$indexDefinition = array();
		$r = $this->sqlQuery->query("SELECT * FROM pg_indexes WHERE schemaname='public' AND tablename=?",$tableName);
		
		foreach ($r as $line){
			$result[$line['indexname']] = $line['indexdef'];
		}
		return $result;
	}
	
	public function getConstraints($tableName){
		$result = array();
		$sql = "SELECT * FROM information_schema.constraint_column_usage WHERE table_name=?";
		$r = $this->sqlQuery->query($sql,$tableName);
		foreach($r as $line){
			$result[$line['constraint_name']][] = $line['column_name'];
		}
		return $result;
		
	}
	
	
}