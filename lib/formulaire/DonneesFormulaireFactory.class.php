<?php

require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaire.class.php");

class DonneesFormulaireFactory{
	
	private $documentTypeFactory;
	private $workspacePath;
	
	public function __construct(DocumentTypeFactory $documentTypeFactory, $workspacePath){
		$this->documentTypeFactory = $documentTypeFactory;
		$this->workspacePath = $workspacePath;
	}
	
	public function get($id_document,$document_type){
		$formulaire = $this->documentTypeFactory->getDocumentType($document_type)->getFormulaire();
		return $this->getFromCache($id_document, $formulaire);
	}
		
	public function getEntiteFormulaire($id_e){
		$formulaire = $this->documentTypeFactory->getEntiteConfig($id_e)->getFormulaire();
		return $this->getFromCache($id_e, $formulaire); 
	}
	
	private function getFromCache($id_document,Formulaire $formulaire){
		static $cache;
		if (empty($cache[$id_document])){
			$cache[$id_document] = new DonneesFormulaire( $this->workspacePath  . "/$id_document.yml", $formulaire);
		}
		return $cache[$id_document];
	}
	
	
	
}