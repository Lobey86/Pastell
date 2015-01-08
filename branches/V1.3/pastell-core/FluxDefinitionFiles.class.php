<?php
//Chargement des fichier definition.yml dans les modules
class FluxDefinitionFiles {
	
	const DEFINITION_FILENAME = "definition.yml";
	
	private $extensions;
	private $yml_loader;
	
	public function __construct(Extensions $extensions, YMLLoader $yml_loader){
		$this->extensions = $extensions;
		$this->yml_loader = $yml_loader;
	}
	
	public function getAll(){
		$result = array();
		$all_module = $this->extensions->getAllModule();
		foreach ($all_module as $module_path){			
			$file_config = $module_path."/".self::DEFINITION_FILENAME;
			$config = $this->yml_loader->getArray($file_config);	
			$id_flux = basename(dirname($file_config));		
			$result[$id_flux] = $config;
		}
		return $result;
	}
	
	public function getInfo($id_flux){
		$module_path = $this->extensions->getModulePath($id_flux);
		return $this->yml_loader->getArray("$module_path/".self::DEFINITION_FILENAME);
	}
	
	public function getAutoAction(){ 
		$result = array();	
		foreach ( $this->getAll() as $typeName => $config){
			$autoAction = $this->getFluxDocumentType($typeName)->getAction()->getAutoAction();
			if ($autoAction){
				$result[$typeName] = $autoAction;
			}
		}
		return $result;
	}
}