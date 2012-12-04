<?php
require_once ( PASTELL_PATH . "/ext/spyc.php");
require_once ( PASTELL_PATH . "/lib/document/DocumentType.class.php");

//Responsabilité: Appeller les bons objects qui connaissent l'emplacement des fichier de conf
//et construire un DocumentType
//(documents, entités, propriétés globales)
class DocumentTypeFactory {
	
	const DEFAULT_DOCUMENT_TYPE_FILE = "default.yml";
	
	private $documentTypeDirectory;
	private $formulaireDefinition;
	private $module_path;
	private $connecteurDefinitionFiles;
	private $fluxDefinitionFiles;
	
	public function __construct($document_type_path,$module_path,ConnecteurDefinitionFiles $connecteurDefinitionFiles,FluxDefinitionFiles $fluxDefinitionFiles){
		$this->documentTypeDirectory = $document_type_path;
		$this->module_path = $module_path;
		$this->connecteurDefinitionFiles = $connecteurDefinitionFiles;
		$this->fluxDefinitionFiles = $fluxDefinitionFiles;
	}
	
	public function getDocumentType($type){
		return new DocumentType($type,$this->getDocumentTypeContent($type));
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
			throw new Exception("Impossible de trouver la défintion du flux $id_flux");
		}
		return new DocumentType($id_flux,$flux_definition);
	}
	
	public function getAllType(){
		return $this->fluxDefinitionFiles->getAllType();
	}

	
	/***********************/
	
	public function isTypePresent($type){
		$all = $this->getAllType();
		return isset($all[$type]);
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
	
	/************************/
	
	private function getDocumentTypeContent($type){
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
		
		$this->formlulaireDefinition[$type] = $this->getYMLFile($filename);	
		
	}
	
	private function getYMLFile($filename){
		$result = apc_fetch("yml_cache_$filename");
		if (!$result){
			$result = Spyc::YAMLLoad($filename);
			apc_store("yml_cache_$filename",$result);
		}		
		return $result; 
	}
	
	private function getTypeDocument(){
		$result = array();
		$allType = $this->getAllType();
		foreach($allType as $type => $docType){
			foreach($docType as $name => $value){
				$result[] = $name;
			}
		}
		return $result;	
	}
	
}