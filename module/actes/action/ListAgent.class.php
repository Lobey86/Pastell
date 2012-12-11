<?php 

class ListAgent extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		$id_a = $recuperateur->get('id_a');

		$siren =  $this->objectInstancier->EntiteSQL->getSiren($this->id_e);
		$info = $this->objectInstancier->AgentSQL->getInfo($id_a,$siren);
		
		$data['matricule_de_lagent'] = $info['matricule'];
		$data['prenom'] = $info['prenom'];
		$data['nom_patronymique'] = $info['nom_patronymique'];
		$status = array('titulaire' => 0,'stagiaire'=>1 , 
						'non-titulaire' => 2);
		$data['statut']  = 0;
		$data['grade'] = $info['emploi_grade_libelle'];
		
		$info = $this->objectInstancier->Document->getInfo($this->id_d);
		
		$donneesFormulaire = $this->objectInstancier->DonneesFormulaireFactory->get($this->id_d,$info['type']);
		$donneesFormulaire->setTabData($data);
	}
	
	public function displayAPI(){
		$siren =  $this->objectInstancier->EntiteSQL->getSiren($this->id_e);
		$listAgent = $this->objectInstancier->AgentSQL->getAll($siren);
		foreach($listAgent as $agent){
			$result[$agent['id_a']] = $agent;
		}
		$this->objectInstancier->JSONoutput->display($result);
	}
	
	public function display(){
		$recuperateur = new Recuperateur($_GET);
		$offset = $recuperateur->getInt('offset',0);
		$search = $recuperateur->get('search');

		$info = $this->objectInstancier->Document->getInfo($this->id_d);
		$this->titre = $info['titre'];
		
		$siren =  $this->objectInstancier->EntiteSQL->getSiren($this->id_e);
		$this->nbAgent = $this->objectInstancier->AgentSQL->getNbAgent($siren,$search);
		$this->listAgent = $this->objectInstancier->AgentSQL->getBySiren($siren,$offset,$search);

		$this->search = $search;
		$this->offset = $offset;
		
		$this->renderPage("Choix d'un agent", "AgentListSelect");
	}
	
}