<?php
require_once ( PASTELL_PATH . "/ext/spyc.php");
require_once ( PASTELL_PATH . "/lib/document/DocumentType.class.php");

class DocumentTypeFactory {
	
	const DEFAULT_DOCUMENT_TYPE_FILE = "default.yml";
	const INDEX_FILE = "index.yml";
	
	private $documentTypeDirectory;
	private $formulaireDefinition;
	private $module_path;
	
	public function __construct($document_type_path,$module_path){
		$this->documentTypeDirectory = $document_type_path;
		$this->module_path = $module_path;
	}
	
	public function getAllType(){
		return $this->index = Spyc::YAMLLoad($this->documentTypeDirectory . self::INDEX_FILE);
	}
	
	public function isTypePresent($type){
		$all = $this->getAllType();
		return isset($all[$type]);
	}
	
	public function getTypeDocument(){
		$result = array();
		$allType = $this->getAllType();
		foreach($allType as $type => $docType){
			foreach($docType as $name => $value){
				$result[] = $name;
			}
		}
		return $result;	
	}
	
	public function getDocumentType($type){
		return new DocumentType($type,$this->getDocumentTypeContent($type));
	}
	
	public function getDocumentTypeContent($type){
		if (! isset($this->formlulaireDefinition[$type])){
			$this->loadFile($type);
		}
		return $this->formlulaireDefinition[$type] ;
	}
	
	private function loadFile($type){
		
		if (file_exists($this->module_path."/$type/definition.yml")){
			$filename = $this->module_path."/$type/definition.yml";
		} else {
			$filename = $this->documentTypeDirectory."/$type.yml";
		}
		if (! file_exists($filename)){
			$filename = $this->documentTypeDirectory . self::DEFAULT_DOCUMENT_TYPE_FILE;
		}	
		$this->formlulaireDefinition[$type] = Spyc::YAMLLoad($filename);	
	}
	
	public function getAllAction(){ 
		$result = array();	
		foreach ( $this->getTypeDocument() as $typeName){
			$action = $this->getDocumentType($typeName)->getAction();
			foreach ($action->getAll() as $actionName){
				$result[$actionName] = $action->getActionName($actionName);
			}
		}
		return $result;
	}
	
	public function getActionByRole($allDroit){
		foreach($allDroit as $droit){
			$r = explode(":",$droit);
			$allType[$r[0]] = true;
		}
		$allType = array_keys($allType);
		foreach($allType as $typeName){
			$action = $this->getDocumentType($typeName)->getAction();
			foreach ($action->getAll() as $actionName){
				$affiche_name = $action->getActionName($actionName);
				$result[$typeName][$affiche_name][] = $actionName;
			}
		}
		
		return $result;
	}
	
	public function getAutoAction(){ 
		$result = array();	
		foreach ( $this->getTypeDocument() as $typeName){
			$autoAction = $this->getDocumentType($typeName)->getAction()->getAutoAction();
			if ($autoAction){
				$result[$typeName] = $autoAction;
			}
		}
		return $result;
	}
	
	public function getTypeByDroit($allDroit){
		foreach($allDroit as $droit){
			$r = explode(":",$droit);
			$allType[$r[0]] = true;
		}
		$allType = array_keys($allType);
		foreach($this->getAllType() as $type_flux => $les_flux){
			foreach($les_flux as $nom => $affichage) {
				if (in_array($nom,$allType)){
					$result[$nom] = $affichage;
				}				
			}
		}
		return $result;
	}
	
}