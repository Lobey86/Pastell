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
		if (! $donneesFormulaire){
			throw new Exception("Aucun connecteur classification-cdg (flux actes-cdg) trouvé pour le centre de gestion de cette entité");
		}
		
		$this->classifCDG = $donneesFormulaire->get("classification_cdg");
		
		$this->renderPage("Fichier de nomemclauture", "NomemclatureListeSelect");
		
	}
	
	
}