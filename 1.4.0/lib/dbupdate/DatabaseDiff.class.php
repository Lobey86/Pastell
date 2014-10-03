<?php

require_once("DatabaseEvent.interface.php");

class DatabaseDiff {

	private $databaseEvent;
	
	public function __construct(DatabaseEvent $databaseEvent){
		$this->databaseEvent = $databaseEvent;
	}
	
	public function getDiff(array $db1,array $db2) {
		
		foreach($db1 as $tableName => $tableDefinition){
			if (empty($db2[$tableName])){
				$this->databaseEvent->onCreateTable($tableName,$tableDefinition);
				continue;
			}
			
			$this->tableDiff($tableName,$tableDefinition,$db2[$tableName]);
			unset($db2[$tableName]);	
		}
		
		foreach($db2 as $tableName => $tableDefinition){
			$this->databaseEvent->onDropTable($tableName,$tableDefinition);
		}
    	return $this->databaseEvent->getSQLCommand();
	} 
	
	private function tableDiff($tableName,$table1,$table2){
			
		if ($table1['Engine'] != $table2['Engine']){
			$this->databaseEvent->onChangeEngine($tableName,$table1['Engine'],$table2['Engine']);
		}
		foreach($table1['Column'] as  $colName => $colDefinition){		
			if (empty($table2['Column'][$colName])){
				$this->databaseEvent->onAddColumn($tableName,$colName,$colDefinition);
				continue;
			} 
			
			$this->isSameColumn($tableName,$colName,$colDefinition,$table2['Column'][$colName]);	
			unset($table2['Column'][$colName]);
		}
		foreach($table2['Column'] as $colName => $def){
			$this->databaseEvent->onDropColumn($tableName,$colName);
		}	
		$this->indexDiff($tableName,$table1['Index'],$table2['Index']);
	}
	
	
	private function indexDiff($tableName,$index1,$index2){
		
		$index1 = $this->canonicalizeIndexName($index1);
		$index2 = $this->canonicalizeIndexName($index2);
		foreach($index1 as $name => $indexDefinition){
			if (empty($index2[$name])){
				$this->databaseEvent->onAddIndex($tableName,$indexDefinition);
				continue;
			}
			if ($index2[$name]['name'] != $index1[$name]['name']){
				$this->databaseEvent->onChangeIndexName($tableName,$index2[$name]['name'],$index1[$name]['name'],$indexDefinition);
			}
			unset($index2[$name]);
		}
		foreach($index2 as $name => $indexDefinition){
			$this->databaseEvent->onDropIndex($tableName,$indexDefinition['name']);
		}
		
	}
	
	private function canonicalizeIndexName($indexDefinition){
		$canoniqueForm  = array();
		foreach($indexDefinition as $name => $def){
			$new_name = md5(serialize($def));
			$canoniqueForm[$new_name] = $def;
			$canoniqueForm[$new_name]['name'] = $name;
		}
		return $canoniqueForm;
	}
	
	private function isSameColumn($tableName,$colName,$colDefinition1,$colDefinition2){
		foreach($colDefinition1 as $type => $value){
			if ($colDefinition2[$type] != $value){
				$this->databaseEvent->onChangeColumn($tableName,$colName,$colDefinition1,$colDefinition2);
				return;
			}
		}
	}	
}