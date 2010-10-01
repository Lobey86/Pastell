<?php

require_once (PASTELL_PATH . "/ext/spyc.php");


class DocumentType {
	
	private $documentTypeDirectory;
	
	public function __construct($documentTypeDirectory){
		$this->documentTypeDirectory = $documentTypeDirectory; 
	}
	
	
	public function getAllTtype(){
		$result = array();
		
		$files = scandir($this->documentTypeDirectory);
		
		foreach($files as $file){
			$ext = substr($file,strrpos($file,"."));	
			$type = substr($file,0,-4);	
			
			if ($ext == ".yml"){
				$result[$type] = $this->getName($type);
			}
		}
		
		return $result;
		
	}

	public function getName($type){
		$array = Spyc::YAMLLoad($this->documentTypeDirectory."/$type.yml");
		$keys = array_keys($array);
		return $keys[0];
	}
	
}