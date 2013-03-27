<?php 
class IparapheurSousType extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		$sous_type_iparapheur = $recuperateur->get('iparapheur_sous_type');
		
		$signature_config = $this->getConnecteurConfigByType('signature');
		$type_iparapheur = $signature_config->get('iparapheur_type');

		$donneesFormulaire = $this->getDonneesFormulaire();
		$donneesFormulaire->setData('iparapheur_type',$type_iparapheur);
		$donneesFormulaire->setData('iparapheur_sous_type',$sous_type_iparapheur);
	}
	
	public function displayAPI(){
		return $this->getSousType();
	}
	
	public function display(){
		$this->sous_type = $this->getSousType();
		$this->renderPage("Choix d'un type de document", "IparapheurSousType");
	}
	
	private function getSousType(){
		$signature = $this->getConnecteur('signature');
		return $signature->getSousType();
	}
}