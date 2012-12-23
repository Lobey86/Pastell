<?php
require_once ( PASTELL_PATH . "/ext/spyc.php");
require_once ( PASTELL_PATH . "/lib/document/DocumentType.class.php");

//Responsabilité: Appeller les bons objects qui connaissent l'emplacement des fichier de conf
//et construire un DocumentType
//(documents, entités, propriétés globales)
class DocumentTypeFactory {
	
	private $module_path;
	private $connecteurDefinitionFiles;
	private $fluxDefinitionFiles;
	
	public function __construct($module_path,ConnecteurDefinitionFiles $connecteurDefinitionFiles,FluxDefinitionFiles $fluxDefinitionFiles){
		$this->module_path = $module_path;
		$this->connecteurDefinitionFiles = $connecteurDefinitionFiles;
		$this->fluxDefinitionFiles = $fluxDefinitionFiles;
	}
	
	public function getGlobalDocumentType($id_connecteur){
		$connecteur_definition = $this->connecteurDefinitionFiles->getInfoGlobal($id_connecteur); 
		if (!$connecteur_definition){
			throw new Exception("Impossible de trouver le connecteur");
		}
		return new DocumentType($id_connecteur,$connecteur_definition);
	}
	
	public function getEntiteDocumentType($id_connecteur){
		$connecteur_definition = $this->connecteurDefinitionFiles->getInfo($id_connecteur); 
		if (!$connecteur_definition){
			throw new Exception("Impossible de trouver le connecteur $id_connecteur");
		}
		return new DocumentType($id_connecteur,$connecteur_definition);
	}
	
	public function getFluxDocumentType($id_flux){
		$flux_definition = $this->fluxDefinitionFiles->getInfo($id_flux);
		if (!$flux_definition){
			return new DocumentType($id_flux,array());
			//throw new Exception("Impossible de trouver la définition du flux $id_flux");
		}
		return new DocumentType($id_flux,$flux_definition);
	}
	
	public function getAllType(){
		return $this->fluxDefinitionFiles->getAllType();
	}


	public function getActionByRole($allDroit){
		foreach($allDroit as $droit){
			$r = explode(":",$droit);
			$allType[$r[0]] = true;
		}
		$allType = array_keys($allType);
		foreach($allType as $typeName){
			try {
				$action = $this->getFluxDocumentType($typeName)->getAction();
			} catch (Exception $e ){
				continue;
			}
			$a_wf = $action->getWorkflowAction();
			if ($a_wf){
				$result[$typeName] = $a_wf;
			} 
		
		}
		
		return $result;
	}
	
	
}