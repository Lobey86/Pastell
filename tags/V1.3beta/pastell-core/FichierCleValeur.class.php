<?php

require_once (PASTELL_PATH . "/ext/spyc.php");

/**
 * S'occupe des relations avec les fichiers YML enregistrant les données de documents 
 * C'est cette classe qui formate correctement les données avant de les enregistré au format YML 
 * et c'est elle qui récupère les données avant de les reformatés dans l'autre sens
 */
class FichierCleValeur {
	
	private $filePath;
	private $info;
	
	public function __construct($filePath){
		$this->filePath = $filePath;
		if ( ! file_exists($this->filePath)){
			return ;
		}
		$this->info = Spyc::YAMLLoad($this->filePath) ;
		
		foreach($this->info as $field_name => $field_value){
			if (is_array($field_value)){
				foreach($field_value as $i => $value){
					$this->info[$field_name][$i] = $this->unescape($value);
				}
			} else {
				$this->info[$field_name] = $this->unescape($field_value);
			}
		}
	}
	
	//La conversion YML efface parfois des caractères lorsque ceux-ci peuvent être transformés en autre chose que des 
	//chaînes de charactères : +, +2, "false", etc.. 
	private function escape($string){
		return addslashes('"'.$string.'"');
	}
	
	private function unescape($string){
		$word = stripslashes($string);
		if(!$word){
			return "";
		}
		if ($word[0] == '"' && substr($word,-1) == '"') {
			$word = substr($word, 1);
			$word = substr($word,0,-1);
		}
		return $word;
	}
	
	public function save(){
		foreach($this->info as $field_name => $field_value){
			if (is_array($field_value)){
				foreach($field_value as $i => $value){
					$result[$field_name][$i] = $this->escape($value);
				}
			} else {
				$result[$field_name] = $this->escape($field_value);
			}
		}
	
		$dump = Spyc::YAMLDump($result);
		file_put_contents($this->filePath,$dump);
	}
	
	public function getInfo(){		
		return $this->info;
	}
	
	public function get($key){
		if (isset($this->info[$key])){
			return $this->info[$key];
		} else {
			return false;
		}
	}
	
	public function set($key,$value){
		$this->info[$key] = $value;
	}
	
	public function exists($key){
		return isset($this->info[$key]);
	}
	
	public function getMulti($key,$num = 0){
		return $this->info[$key][$num];
	}
	
	public function setMulti($key,$value,$num = 0){
		$this->info[$key][$num] = $value;
	}
	
	public function addValue($key,$value){
		$this->info[$key][] = $value;
	}
	
	public function count($key){
		return count($this->info[$key]);
	}
	
	public function delete($key,$num){
		array_splice($this->info[$key],$num,1);
	}

	public function deleteField($key){
		unset($this->info[$key]);
	}
}


