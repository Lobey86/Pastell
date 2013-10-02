<?php

require_once("DatabaseEvent.interface.php");

class DatabaseEventText implements DatabaseEvent {
	
	private $sqlCommand;
	
	public function getSQLCommand(){
		return $this->sqlCommand;
	}
	
	public function onCreateTable($tableName,array $tableDefinition){
		$this->sqlCommand[] = "> TABLE $tableName";
	}
	
	public function onDropTable($tableName) { 
		$this->sqlCommand[] = "< TABLE $tableName";
	}
	
	public function onChangeEngine($tableName,$engine1,$engine2){
		$this->sqlCommand[] = "Table $tableName $engine1 => $engine2";
	}
	
	public function onAddColumn($tableName,$columnName,array $columnDefinition) {
		$this->sqlCommand[] = "> Column $tableName.$columnName";
	}
	
	public function onDropColumn($tableName,$columnName) {
		$this->sqlCommand[] = "< Column $tableName.$columnName";
	}
	
	public function onChangeColumn($tableName,$columnName,array $definition1,array $definition2) {
		$this->sqlCommand[] = "<> Column $tableName.$columnName : ".$this->linearDiff($definition1,$definition2);
	}
	
	public function onAddIndex($tableName, array $indexDefinition) {
		$this->sqlCommand[] = "> INDEX $tableName.".$indexDefinition['name'];
	}
	
	public function onDropIndex($tableName,$indexName) {
		$this->sqlCommand[] = "< INDEX $tableName.$indexName";
	}
	
	public function onChangeIndexName($tableName,$oldName,$newName,array $indexDefinition) {
		$this->sqlCommand[] = "<> INDEX $tableName <$oldName >$newName";
	}
	
	private function linearDiff($definition1,$definition2){
		$result = array();
		foreach($definition1 as $type => $value){
			if ($definition2[$type] != $value){
				$result[] = "$type : <$value >{$definition2[$type]}";
			}
		}
		return implode("; ",$result);
	}
}