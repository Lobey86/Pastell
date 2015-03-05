<?php 

class IparapheurType extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		$type_iparapheur = $recuperateur->get('iparapheur_type');
		$connecteur_properties = $this->getConnecteurProperties();
		$connecteur_properties->setData('iparapheur_type',$type_iparapheur);
		$this->objectInstancier->ActionExecutorFactory->executeOnConnecteur($this->id_ce,$this->id_u,'update-sous-type');
	}
	
	public function displayAPI(){
		return $this->getType();
	}
	
	public function display(){
		$signature = $this->objectInstancier->ConnecteurFactory->getConnecteurByType($this->id_e,$this->type,'signature');
		$this->type_iparapheur = $this->getType();
		$this->renderPage("Choix d'un type de document", "IparapheurType");
	}
	
	private function getType(){
		$signature = $this->getMyConnecteur();
		return $signature->getType();
	}
	
}