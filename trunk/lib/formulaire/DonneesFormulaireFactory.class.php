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
		static $cache;
		if (empty($cache[$id_document])){
			$formulaire = $this->documentTypeFactory->getDocumentType($document_type)->getFormulaire();
			$cache[$id_document] = new DonneesFormulaire( $this->workspacePath  . "/$id_document.yml", $formulaire);
		}
		return $cache[$id_document];
	}
	
}