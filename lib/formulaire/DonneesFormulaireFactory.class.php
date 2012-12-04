<?php

require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaire.class.php");

class DonneesFormulaireFactory{
	
	private $documentTypeFactory;
	private $workspacePath;
	private $connecteurEntiteSQL;
	
	
	public function __construct(DocumentTypeFactory $documentTypeFactory, $workspacePath, ConnecteurEntiteSQL $connecteurEntiteSQL){
		$this->documentTypeFactory = $documentTypeFactory;
		$this->workspacePath = $workspacePath;
		$this->connecteurEntiteSQL = $connecteurEntiteSQL;
	}
	
	public function get($id_document,$document_type){
		$formulaire = $this->documentTypeFactory->getDocumentType($document_type)->getFormulaire();
		return $this->getFromCache($id_document, $formulaire);
	}
	
	public function getConnecteurEntiteFormulaire($id_ce){
		$connecteur_entite_info = $this->connecteurEntiteSQL->getInfo($id_ce);
		if ($connecteur_entite_info['id_e']){		
			$documentType = $this->documentTypeFactory->getEntiteDocumentType($connecteur_entite_info['id_connecteur']);
		} else {
			$documentType = $this->documentTypeFactory->getGlobalDocumentType($connecteur_entite_info['id_connecteur']);
		} 
		$id_document = "connecteur_$id_ce";
		return $this->getFromCache($id_document,$documentType->getFormulaire());
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