<?php

class ConnecteurFactory  {
	
	private $connecteur_path;
	private $workspath_path;
	private $documentTypeFactory;
	
	private $lastError;
	
	public function __construct($workspacePath,$connecteur_path,DocumentTypeFactory $documentTypeFactory){
		$this->connecteur_path = $connecteur_path;
		$this->workspace_path = $workspacePath;
		$this->documentTypeFactory = $documentTypeFactory;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	private function getAll($id_e){
		$filename = $this->workspace_path."/connecteur_{$id_e}.yml";
		return $this->loadFile($filename);
	}
	
	
	public function getFluxEntite($id_e){
		$filename = $this->workspace_path."/flux_{$id_e}.yml";
		return $this->loadFile($filename);
	}
	
	public function getConnecteur($id_e,$flux,$type_connecteur){
		$all = $this->getFluxEntite($id_e);
		if (empty($all[$flux][$type_connecteur])){
			$this->lastError = "Impossible de trouver un connecteur $type_connecteur pour le flux $flux";
			return false;
		}
		return $this->getDataFormulaire($id_e, $all[$flux][$type_connecteur]);
	}
	
	
	public function getAllFlux($id_e){
		
		$all_connecteur = $this->documentTypeFactory->getAllConnecteur();
		
		$entite_data = $this->getFluxEntite($id_e);
		$entite_connecteur = $this->getAll($id_e);
		$result = array();
		foreach($all_connecteur as $flux => $needed_connecteur){
			foreach($needed_connecteur as $connecteur){
				
				$line['flux'] = $flux;
				$line['type'] = $connecteur;
				$line['connecteur'] = false;
				$line['libelle'] = false;
				if (isset($entite_data[$flux][$connecteur])){
					
					$line['libelle'] =  $entite_data[$flux][$connecteur];
					$line['connecteur'] = $entite_data[$flux][$connecteur] = $entite_connecteur[$line['libelle']]['name']; 
				}
				$result[] = $line;
			} 
		}
		
		
		return $result;
	}
	
	public function loadFile($filename){
		if (! file_exists($filename)){
			return array();
		}
		return Spyc::YAMLLoad($filename);	
	}
	
	public function loadConnecteurDefinition($id_connecteur){
		$result = glob("{$this->module_path}/*/connecteur/$id_connecteur.yml");
		if (count($result)<1){
			return false;
		}
		return $this->loadFile($result[0]);
	}
	
	public function addConnecteur($id_e,$libelle,$id_connecteur){
		
		$connecteur_definition = $this->loadConnecteurDefinition($id_connecteur);
		
		if (! $connecteur_definition ){
			$this->lastError = "Le connecteur $id_connecteur est inconnu";
			return false;
		}
		
		$all = $this->getAll($id_e);
		$all[$libelle] = array('connecteur_id' => $id_connecteur,'name' => $connecteur_definition['name'],'type'=>$connecteur_definition['type']);
		$this->saveAll($id_e,$all);		
	}
	
	public function saveAll($id_e,$connecteur_definition){
		$dump = Spyc::YAMLDump($connecteur_definition);
		file_put_contents($this->workspace_path."/connecteur_{$id_e}.yml",$dump);
	}
	
	public function getAllDispoEntite($id_e,$type){
		$all_connecteur = $this->getAll($id_e);
		$result = array();
		foreach($all_connecteur as $connecteur_name => $connecteur){
			if ($connecteur['type'] == $type){
				$result[$connecteur_name] = $connecteur;
			}
		}
		return $result;
	}
	
	public function addConnecteur2Flux($id_e,$flux,$type,$id_connecteur){
		$entite_data = $this->getFluxEntite($id_e);
		$entite_data[$flux][$type] = $id_connecteur;
		$dump = Spyc::YAMLDump($entite_data);
		file_put_contents($this->workspace_path."/flux_{$id_e}.yml",$dump);
	}
	
	public function getDocumentType($id_e,$libelle){
		$all_connecteur = $this->getAll($id_e);
		if (empty($all_connecteur[$libelle])){
			$this->lastError = "Impossible de trouver le connecteur $libelle";
			return false;
		}
 		$type = $all_connecteur[$libelle]['type'];
 		$connecteur_id = $all_connecteur[$libelle]['connecteur_id'];
		 
		$definition = $this->loadConnecteurDefinition($connecteur_id);
		
		$definition['formulaire']['page0']['libelle'] = array('name'=>'Libellé'); 
		 
		 return new DocumentType($type,$definition);
	}
	
	
	public function getInfo($id_e,$libelle){
		$all_connecteur = $this->getAll($id_e);
		if (empty($all_connecteur[$libelle])){
			$this->lastError = "Impossible de trouver le connecteur $libelle";
			return false;
		}
		return $all_connecteur[$libelle];
	}
	
	
	public function getDataFormulaire($id_e,$libelle){
		$all_connecteur = $this->getAll($id_e);
		if (empty($all_connecteur[$libelle])){
			$this->lastError = "Impossible de trouver le connecteur $libelle";
			return false;
		}
		$formulaire = $this->getDocumentType($id_e,$libelle)->getFormulaire();
		
		$donneFormulaire = new DonneesFormulaire( $this->workspace_path  . "/connecteur_{$id_e}_$libelle.yml", $formulaire);
		$donneFormulaire->injectData('id_e',$id_e);
		$donneFormulaire->injectData('libelle',$libelle);		
 		return $donneFormulaire;
	}
	
	public function delete($id_e,$libelle){
		$donneFormulaire = $this->getDataFormulaire($id_e, $libelle);
		$donneFormulaire->delete();
		$all = $this->getAll($id_e);
		unset($all[$libelle]);
		$this->saveAll($id_e,$all);		
	}
	
	
}
