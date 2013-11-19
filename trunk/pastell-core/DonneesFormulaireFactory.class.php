<?php


class DonneesFormulaireFactory{
	
	private $documentTypeFactory;
	private $workspacePath;
	private $connecteurEntiteSQL;
	private $documentSQL;
	
	public function __construct(DocumentTypeFactory $documentTypeFactory, 
								$workspacePath, 
								ConnecteurEntiteSQL $connecteurEntiteSQL,
								Document $documentSQL
								){
		$this->documentTypeFactory = $documentTypeFactory;
		$this->workspacePath = $workspacePath;
		$this->connecteurEntiteSQL = $connecteurEntiteSQL;
		$this->documentSQL = $documentSQL;
	}
	
	public function get($id_d,$document_type = false){
		$info = $this->documentSQL->getInfo($id_d);
		if (! $document_type){
			$document_type = $info['type'];
		}
		
		if( !$document_type){
			throw new Exception("Document inexistant");
		}
		
		$formulaire = $this->documentTypeFactory->getFluxDocumentType($document_type)->getFormulaire();
		return $this->getFromCacheNewPlan($id_d, $formulaire);
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
	
	private function getFromCache($id_document,Formulaire $formulaire){
		static $cache;
		if (empty($cache[$id_document])){
			$cache[$id_document] = new DonneesFormulaire( $this->workspacePath  . "/$id_document.yml", $formulaire);
		}
		return $cache[$id_document];
	}
	
	private function getFromCacheNewPlan($id_document,Formulaire $formulaire){
		static $cache;
		if (empty($cache[$id_document])){
			$dir = $this->getNewDirectoryPath($id_document);
			if (! file_exists($dir)) {
				mkdir($dir,0777,true);
			}
			$cache[$id_document] = new DonneesFormulaire("$dir/$id_document.yml", $formulaire);
		}
		return $cache[$id_document];
	}
	
	private function getNewDirectoryPath($id_document){
		if (strlen($id_document) < 2){
			return $this->workspacePath;
		}
		$a = $id_document[0];
		$b = $id_document[1];
		return $this->workspacePath."/$a/$b/";
	}
	
}