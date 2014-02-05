<?php


interface DatabaseEvent {
	
	function getSQLCommand();
	
	function onCreateTable($tableName,array $tableDefinition);
	function onDropTable($table);
	function onChangeEngine($tableName,$engine1,$engine2);
	function onAddColumn($tableName,$columnName,array $columnDefinition);
	function onDropColumn($tableName,$columnName);
	function onChangeColumn($tableName,$columnName,array $definition1,array $definition2);
	function onAddIndex($tableName,array $indexDefinition);
	function onDropIndex($tableName,$indexName);
	function onChangeIndexName($tableName,$oldName,$newName,array $indexDefinition);
	
}