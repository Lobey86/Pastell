<?php


class ConnecteurFactory  {
	
	
	private $module_path;
	private $workspath_path;
	private $documentTypeFactory;
	
	private $lastError;
	
	public function __construct($workspacePath,$module_path,DocumentTypeFactory $documentTypeFactory){
		$this->module_path = $module_path;
		$this->workspace_path = $workspacePath;
		$this->documentTypeFactory = $documentTypeFactory;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function getAll($id_e){
		$filename = $this->workspace_path."/connecteur_{$id_e}.yml";
		return $this->loadFile($filename);
	}
	
	
	public function getFluxEntite($id_e){
		$filename = $this->workspace_path."/flux_{$id_e}.yml";
		return $this->loadFile($filename);
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
	
	public function getAllDispo(){
		$all_connecteur_definition = glob("{$this->module_path}/*/connecteur/*.yml");
		$result = array();
		foreach($all_connecteur_definition as $connecteur_definition){
			$id_connecteur = basename($connecteur_definition,".yml");
			$result[$id_connecteur] = $this->loadFile($connecteur_definition);
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
	
	
}
