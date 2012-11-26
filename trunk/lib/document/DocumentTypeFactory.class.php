<?php
require_once ( PASTELL_PATH . "/ext/spyc.php");
require_once ( PASTELL_PATH . "/lib/document/DocumentType.class.php");

//Responsabilité: définir l'empacement des différents fichiers YML de configuration (documents, entités, propriétés globales)

class DocumentTypeFactory {
	
	const DEFAULT_DOCUMENT_TYPE_FILE = "default.yml";
	
	private $documentTypeDirectory;
	private $formulaireDefinition;
	private $module_path;
	
	public function __construct($document_type_path,$module_path){
		$this->documentTypeDirectory = $document_type_path;
		$this->module_path = $module_path;
	}
	
	public function getAllType(){
		static $all_type;
		if ($all_type){
			return $all_type;
		}
		
		$all = glob("{$this->documentTypeDirectory}/*.yml");
		$exlude = array('default.yml','global-properties.yml','collectivite-properties.yml');
		
		$all_type = array();
		
		foreach ($all as $file_config){
			if( in_array(basename($file_config),$exlude)){
				continue;
			}
			$config = $this->getYMLFile($file_config);	
			$name = basename($file_config,".yml");		
			$type = empty($config['type'])?"Flux Généraux":$config['type'];
			$all_type[$type][$name] = $config['nom'];
		}
			
		
		$all_module = glob("{$this->module_path}/*/definition.yml");
		foreach ($all_module as $file_config){			
			$config = $this->getYMLFile($file_config);	
			$name = basename(dirname($file_config));		
			$type = empty($config['type'])?"Flux Généraux":$config['type'];
			$all_type[$type][$name] = $config['nom'];
		}
		
		return $all_type;
	}
	
	
	public function getDocumentType($type){
		return new DocumentType($type,$this->getDocumentTypeContent($type));
	}
	
	public function getEntiteConfig($id_e){
		if ($id_e){			
			return $this->getGlobalProperties("collectivite-properties");
		} else {
			return $this->getGlobalProperties("global-properties");
		}
	}
	
	private function getGlobalProperties($typename){
		static $documentType;
		if ( ! $documentType ){		
			$full_config = $this->getYMLFile("{$this->documentTypeDirectory}/$typename.yml");
			
			foreach (glob("{$this->module_path}/*/$typename.yml") as $file_config){
				$config = $this->getYMLFile($file_config);			
				
				$type =  basename(dirname($file_config));
				foreach($config['action'] as $action => $value){
					$config['action'][$action]['type'] = $type;
				}
				
				$full_config = array_merge_recursive($full_config, $config);
			}
			$documentType = new DocumentType($typename,$full_config);
		}
		return $documentType;
	}
	
	/***********************/
	
	public function isTypePresent($type){
		$all = $this->getAllType();
		return isset($all[$type]);
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