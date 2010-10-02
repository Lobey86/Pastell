<?php

require_once (PASTELL_PATH . "/ext/spyc.php");
require_once( PASTELL_PATH . "/lib/action/Action.class.php");


class DocumentType {
	
	private $documentTypeDirectory;
	
	private $typeInformation;
	
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

	/*****/
	
	public function getName($type){
		$this->formulaireDefinition = Spyc::YAMLLoad($this->documentTypeDirectory."/$type.yml");
		return $this->formulaireDefinition['nom'];
	}
	
	public function getFormulaire($type){
		$file = $this->documentTypeDirectory."/$type.yml";
		$this->formulaireDefinition = Spyc::YAMLLoad($file);
		return new Formulaire($this->formulaireDefinition['formulaire']);
	}
	
	public function getAction($type){
		$this->formulaireDefinition = Spyc::YAMLLoad($this->documentTypeDirectory."/$type.yml");
		return new Action($this->formulaireDefinition['action']);
	}
	
}