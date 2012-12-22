<?php 

class IparapheurSousType extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		$sous_type_iparapheur = $recuperateur->get('iparapheur_sous_type');
		
		$documentType = $this->objectInstancier->DocumentTypeFactory->getFluxDocumentType($this->type); 
		$type_iparapheur = $documentType->getFormulaire()->getField('iparapheur_type')->getProperties('default');
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		$donneesFormulaire->setData('iparapheur_type',$type_iparapheur);
		$donneesFormulaire->setData('iparapheur_sous_type',$sous_type_iparapheur);
	}
	
	public function displayAPI(){
		$sous_type = $this->getSousType();
		$this->objectInstancier->JSONoutput->display($sous_type);
	}
	
	public function display(){
		$signature = $this->objectInstancier->ConnecteurFactory->getConnecteurByType($this->id_e,$this->type,'signature');

		$documentType = $this->objectInstancier->DocumentTypeFactory->getFluxDocumentType($this->type); 
		$type_iparapheur = $documentType->getFormulaire()->getField('iparapheur_type')->getProperties('default');
		
		$this->sous_type = $this->getSousType();
		$this->renderPage("Choix d'un type de document", "IparapheurSousType");
	}
	
	private function getSousType(){
		$signature = $this->objectInstancier->ConnecteurFactory->getConnecteurByType($this->id_e,$this->type,'signature');

		$documentType = $this->objectInstancier->DocumentTypeFactory->getFluxDocumentType($this->type); 
		$type_iparapheur = $documentType->getFormulaire()->getField('iparapheur_type')->getProperties('default');
		
		return $signature->getSousType($type_iparapheur);
	}
	
	
}