<?php
class ConnecteurDefinitionFiles {
	
	private $connecteur_path;
	
	public function __construct($connecteur_path){
		$this->connecteur_path = $connecteur_path;
	}
	
	public function getAll(){
		$all_connecteur_definition = glob("{$this->connecteur_path}/*/entite-properties.yml");
		$result = array();
		foreach($all_connecteur_definition as $connecteur_definition){
			$id_connecteur = basename(dirname($connecteur_definition));
			$result[$id_connecteur] = $this->loadFile($connecteur_definition);
		}
		return $result;
	}
	
	public function getAllGlobal(){
		$all_connecteur_definition = glob("{$this->connecteur_path}/*/global-properties.yml");
		$result = array();
		foreach($all_connecteur_definition as $connecteur_definition){
			$id_connecteur = basename(dirname($connecteur_definition));
			$result[$id_connecteur] = $this->loadFile($connecteur_definition);
		}
		return $result;
	}
	
	public function getAllByIdE($id_e){
		return $id_e?$this->getAll():$this->getAllGlobal();
	}
	
	public function getInfo($id_connecteur){
		return $this->loadFile("{$this->connecteur_path}/$id_connecteur/entite-properties.yml");
	}
	
	public function getInfoGlobal($id_connecteur){
		return $this->loadFile("{$this->connecteur_path}/$id_connecteur/global-properties.yml");
	}
	
	public function loadFile($filename){
		if (! file_exists($filename)){
			return array();
		}
		return Spyc::YAMLLoad($filename);	
	}
	
}