<?php

require_once( PASTELL_PATH . "/lib/entite/EntiteRelation.class.php");

require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class AccepterFournisseur extends ActionExecutor {

	public function go(){
		$documentEntite = new DocumentEntite($this->getSQLQuery());
		$id_fournisseur = $documentEntite->getEntiteWithRole($this->id_d,"editeur");
		
		$entiteRelation = new EntiteRelation($this->getSQLQuery());
		$entiteRelation->addRelation($id_fournisseur,EntiteRelation::IS_FOURNISSEUR,$this->id_e);
		
		
		$entite = new Entite($this->getSQLQuery(),$id_fournisseur);
		$infoEntite = $entite->getInfo();
		$nomFournisseur = $infoEntite['denomination'];
		
		$entite = new Entite($this->getSQLQuery(),$this->id_e);
		$infoEntite = $entite->getInfo();
		$nomCol = $infoEntite['denomination'];
		
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"L'inscription de $nomFournisseur a été accepté");
		$this->getActionCreator()->addToEntite($id_fournisseur,"$nomCol a accepté l'inscription");
		
		$this->setLastMessage("Le fournisseur a été accepté.");
		return true;			
	}
}