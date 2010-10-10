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
		return $this->index = Spyc::YAMLLoad($this->documentTypeDirectory."/index.yml");
		
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
	public function getFormulaireDefinition($type){
		if (! isset($this->formlulaireDefinition[$type])){
			$filename = $this->documentTypeDirectory."/$type.yml";
			if (! file_exists($filename)){
				$filename = $this->documentTypeDirectory."/default.yml";
			}	
			
			
			$this->formlulaireDefinition[$type] = Spyc::YAMLLoad($filename);
		}
		return $this->formlulaireDefinition[$type] ;
	}
	
	
	public function getName($type){
		$tabDef = $this->getFormulaireDefinition($type);
		return $tabDef['nom'];
	}
	
	public function getFormulaire($type){
		$tabDef = $this->getFormulaireDefinition($type);
		return new Formulaire($tabDef['formulaire']);
	}
	
	public function getAction($type){
		$tabDef = $this->getFormulaireDefinition($type);
		return new Action($tabDef['action']);
	}
	
	
}