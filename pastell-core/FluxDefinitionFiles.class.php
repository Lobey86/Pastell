<?php

//Chargé des fichier definition.yml
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
	
	public function getAllType(){
		static $result;
		
		if ($result){
			return $result;
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
	
	public function isTypePresent($type){
		$all = $this->getAllType();
		return isset($all[$type]);
	}
	
	/**TODO sans doute pas à la bonne place **/
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