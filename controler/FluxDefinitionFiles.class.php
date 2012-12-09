<?php

//Chargé des fichier definition.yml
class FluxDefinitionFiles {
	
	private $module_path;
	
	public function __construct($module_path){
		$this->module_path = $module_path;
	}
	
	public function getAll(){
		$result = array();
		
		$all_module = glob("{$this->module_path}/*/definition.yml");
		foreach ($all_module as $file_config){			
			$config = $this->loadFile($file_config);	
			$id_flux = basename(dirname($file_config));		
			$result[$id_flux] = $config;
		}
		return $result;
	}
	
	public function getInfo($id_flux){
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
		foreach($all_type as $type => $flux){
			asort($all_type[$type]);
		}
		asort($all_type);
		
		$result["Flux Généraux"] =  $all_type["Flux Généraux"];
		unset($all_type["Flux Généraux"]);
		$result = $result + $all_type;
		return $result;
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