<?php
//Chargement des fichier definition.yml dans les modules
class FluxDefinitionFiles {
	
	private $module_path;
	private $yml_loader;
	
	public function __construct($module_path, YMLLoader $yml_loader){
		$this->module_path = $module_path;
		$this->yml_loader = $yml_loader;
	}
	
	public function getAll(){
		$result = array();
		$all_module = glob("{$this->module_path}/*/definition.yml");
		foreach ($all_module as $file_config){			
			$config = $this->yml_loader->getArray($file_config);	
			$id_flux = basename(dirname($file_config));		
			$result[$id_flux] = $config;
		}
		return $result;
	}
	
	public function getInfo($id_flux){
		return $this->yml_loader->getArray("{$this->module_path}/$id_flux/definition.yml");
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