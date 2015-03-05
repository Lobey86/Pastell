<?php 

class CreationDocumentChoixRecuperation extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		
		$id_ce = $recuperateur->get('connecteur_creation');
		$info = $this->objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
		
		if ($info['type'] != 'RecuperationFichier'){
			throw new Exception("Le connecteur choisi n'est pas un connecteur de récupération");
		}

		if ($info['id_e'] != $this->id_e){
			throw new Exception("Le connecteur choisi n'appartient pas à la bonne collectivité");
		}
		$connecteur_properties = $this->getConnecteurProperties();
		$connecteur_properties->setData('connecteur_recup',$info['libelle']);
		$connecteur_properties->setData('connecteur_recup_id',$id_ce);
		
		return true;
	}
	
	public function displayAPI(){
		return $this->getInfo();
	}
	
	public function display(){
		$this->recuperation_connecteur_list = $this->getInfo();
		$this->renderPage("Choix d'un connecteur", "CreationDocumentChoixConnecteur");
	}
	
	private function getInfo(){
		$all =  $this->objectInstancier->ConnecteurEntiteSQL->getByType($this->id_e,"RecuperationFichier");
		$result = array();
		foreach($all as $line){
			$result[$line['id_ce']] = $line['libelle'];
		}
		return $result;
	}
	
}