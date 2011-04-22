<?php
require_once ( PASTELL_PATH . "/ext/spyc.php");
require_once ( PASTELL_PATH . "/lib/document/DocumentType.class.php");

class DocumentTypeFactory {
	
	const DEFAULT_DIRECTORY  =  "/document-type/";
	const DEFAULT_DOCUMENT_TYPE_FILE = "default.yml";
	const INDEX_FILE = "index.yml";
	
	private $documentTypeDirectory;
	private $formulaireDefinition;
	
	public function __construct(){
		$this->setDocumentTypeDirectory( PASTELL_PATH . self::DEFAULT_DIRECTORY );
	}
	
	public function setDocumentTypeDirectory($documentTypeDirectory){
		$this->documentTypeDirectory = $documentTypeDirectory;
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
		$filename = $this->documentTypeDirectory."/$type.yml";
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
				$result[$actionName] = $action->getActionName($actionName);
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
		return $allType;
	}
	
}