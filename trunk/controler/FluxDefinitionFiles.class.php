<?php
class FluxDefinitionFiles {
	
	private $flux_path;
	
	public function __construct($flux_path){
		$this->flux_path = $flux_path;
	}
	
	public function getAll(){
		$all_flux_definition = glob("{$this->flux_path}/*/definition.yml");
		$result = array();
		foreach($all_flux_definition as $flux_definition){
			$id_flux = basename(dirname($flux_definition));
			$result[$id_flux] = $this->loadFile($flux_definition);
		}
		return $result;
	}
	
	public function getInfo($id_flux){
		return $this->loadFile("{$this->flux_path}/$id_flux/definition.yml");
	}
	
	public function loadFile($filename){
		if (! file_exists($filename)){
			return array();
		}
		return Spyc::YAMLLoad($filename);	
	}

	
}