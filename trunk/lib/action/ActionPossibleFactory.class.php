<?php

require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");

class ActionPossibleFactory {
	
	public function __construct($sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getInstance($id_u,$id_e,$id_d,$type,$donneesFormulaire,$roleUtilisateur) {
		
		$documentTypeFactory = new DocumentTypeFactory();
		$documentType = $documentTypeFactory->getDocumentType($type);
		
		$formulaire = $documentType->getFormulaire();
		$theAction = $documentType->getAction();
		
		$actionPossible = new ActionPossible($this->sqlQuery,$id_e,$id_u,$theAction);
			
		$actionPossible->setDocumentActionEntite( new DocumentActionEntite($this->sqlQuery));
		$actionPossible->setDocumentEntite( new DocumentEntite($this->sqlQuery));
		$actionPossible->setRoleUtilisateur($roleUtilisateur);
		$actionPossible->setDonnesFormulaire($donneesFormulaire);
		$actionPossible->setEntite(new Entite($this->sqlQuery,$id_e));
		return $actionPossible;
		
	}
	
}