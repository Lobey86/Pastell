<?php

//Chargé des fichier entite-properties.yml et global-properties.yml

class ConnecteurDefinitionFiles {
	
	private $connecteur_path;
	private $yml_loader;
	
	public function __construct($connecteur_path,YMLLoader $yml_loader){
		$this->connecteur_path = $connecteur_path;
		$this->yml_loader = $yml_loader;
	}
	
	public function getAll(){
		$all_connecteur_definition = glob("{$this->connecteur_path}/*/entite-properties.yml");
		$result = array();
		foreach($all_connecteur_definition as $connecteur_definition){
			$id_connecteur = basename(dirname($connecteur_definition));
			$result[$id_connecteur] = $this->yml_loader->getArray($connecteur_definition);
		}
		return $result;
	}
	
	public function getAllGlobal(){
		$all_connecteur_definition = glob("{$this->connecteur_path}/*/global-properties.yml");
		$result = array();
		foreach($all_connecteur_definition as $connecteur_definition){
			$id_connecteur = basename(dirname($connecteur_definition));
			$result[$id_connecteur] = $this->yml_loader->getArray($connecteur_definition);
		}
		return $result;
	}
	
	public function getAllByIdE($id_e){
		return $id_e?$this->getAll():$this->getAllGlobal();
	}
	
	public function getInfo($id_connecteur){		
		return $this->yml_loader->getArray("{$this->connecteur_path}/$id_connecteur/entite-properties.yml");
	}
	
	public function getInfoGlobal($id_connecteur){
		return $this->yml_loader->getArray("{$this->connecteur_path}/$id_connecteur/global-properties.yml");
	}
	
	
	public function getConnecteurClass($id_connecteur){
		$all = glob("{$this->connecteur_path}/$id_connecteur/*.class.php");
		if (! $all){
			throw new Exception("Impossible de trouver une classe pour le connecteur $id_connecteur");
		}
		$class_file = $all[0];
		$class_name = basename($class_file,".class.php");
		require_once($class_file);
		return $class_name;
	}
	
}