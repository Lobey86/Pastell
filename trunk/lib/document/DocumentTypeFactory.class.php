<?php
require_once ( PASTELL_PATH . "/ext/spyc.php");
require_once ( PASTELL_PATH . "/lib/document/DocumentType.class.php");

class DocumentTypeFactory {
	
	const DEFAULT_DIRECTORY  =  "/document-type/";
	const DEFAULT_DOCUMENT_TYPE_FILE = "default.yml";
	
	private $documentTypeDirectory;
	private $formulaireDefinition;
	
	public function __construct(){
		$this->setDocumentTypeDirectory( PASTELL_PATH . self::DEFAULT_DIRECTORY );
	}
	
	public function setDocumentTypeDirectory($documentTypeDirectory){
		$this->documentTypeDirectory = $documentTypeDirectory;
	}
	
	public function getAllTtype(){
		return $this->index = Spyc::YAMLLoad($this->documentTypeDirectory."/index.yml");
	}
	
	public function getDocumentType($type){
		return new DocumentType($type,$this->getDocumentTypeContent($type));
	}
	
	private function getDocumentTypeContent($type){
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
}