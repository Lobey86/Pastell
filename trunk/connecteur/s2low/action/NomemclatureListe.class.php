<?php
class NomemclatureListe extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_GET);
		$fieldValue = $recuperateur->get($this->field);
		
		$donneesFormulaire = $this->objectInstancier->ConnecteurFactory->getConnecteurConfig($this->id_ce);
		$donneesFormulaire->setData($this->field,$fieldValue);
	}
	
	public function displayAPI(){}
	
	public function display(){
				
		$infoCDG = $this->objectInstancier->EntiteSQL->getCDG($this->id_e);
		$donneesFormulaire = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($infoCDG['id_e'],'actes-cdg','classification-cdg');

		$this->classifCDG = $donneesFormulaire->get("classification_cdg");
		
		$this->renderPage("Fichier de nomemclauture", "NomemclatureListeSelect");
		
	}
	
	
}