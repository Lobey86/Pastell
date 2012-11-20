<?php

require_once( PASTELL_PATH. "/lib/document/DocumentTypeFactory.class.php");
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
		return new DonneesFormulaire( $this->workspacePath  . "/$id_document.yml", $formulaire);
	}
	
}