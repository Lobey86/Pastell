<?php

require_once("DatabaseEventText.class.php");
require_once("DatabaseEventMySQL.class.php");

class DatabaseEventFactory {
	
	
	public static function getInstance($type){
		if (! $type){
			return new DatabaseEventMySQL();
		}
		
		switch($type){
			case 'mysql': return new DatabaseEventMySQL();
			case 'text': return new DatabaseEventText();
		}
		throw new Exception("Type de base '$databaseType' non supporté\n");
		
	}
	
}