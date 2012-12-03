<?php

//Chargé des fichier definition.yml
class FluxDefinitionFiles {
	
	public function __construct($document_type_path,$module_path){
		$this->documentTypeDirectory = $document_type_path;
		$this->module_path = $module_path;
	}
	
	public function getAll(){
		$all = glob("{$this->documentTypeDirectory}/*.yml");
		$exlude = array('default.yml','global-properties.yml','collectivite-properties.yml');
		
		$all_type = array();
		
		foreach ($all as $file_config){
			if( in_array(basename($file_config),$exlude)){
				continue;
			}
			$config = $this->loadFile($file_config);	
			$id_flux = basename($file_config,".yml");		
			$result[$id_flux] = $config;
		}
			
		
		$all_module = glob("{$this->module_path}/*/definition.yml");
		foreach ($all_module as $file_config){			
			$config = $this->loadFile($file_config);	
			$id_flux = basename(dirname($file_config));		
			$result[$id_flux] = $config;
		}
		return $result;
	}
	
	public function getInfo($id_flux){
		if (file_exists("{$this->documentTypeDirectory}/$id_flux.yml")){
			return $this->loadFile("{$this->documentTypeDirectory}/$id_flux.yml");
		}
		if (file_exists("{$this->module_path}/$id_flux/definition.yml")){
			return $this->loadFile("{$this->module_path}/$id_flux/definition.yml");
		}
		throw new Exception("Impossible de trouver la définition du flux $id_flux");		
	}
	
	public function getAllType(){
		static $all_type;
		if ($all_type){
			return $all_type;
		}
		$all_type = array();
		foreach ($this->getAll() as $id_flux => $properties){			
			$type = empty($properties['type'])?"Flux Généraux":$properties['type'];
			$all_type[$type][$id_flux] = $properties['nom'];
		}
		return $all_type;
	}
	
	private function loadFile($filename){
		if (! file_exists($filename)){
			return array();
		}
		return $this->getYMLFile($filename);	
	}
	
	private function getYMLFile($filename){
		$result = apc_fetch("yml_cache_$filename");
		if (!$result){
			$result = Spyc::YAMLLoad($filename);
			apc_store("yml_cache_$filename",$result);
		}		
		return $result; 
	}
	
}