<?php

class RefuserModerateur extends ActionExecutor {

	public function go(){
		$documentEntite = new DocumentEntite($this->getSQLQuery());
		$id_fournisseur = $documentEntite->getEntiteWithRole($this->id_d,"editeur");

		$entite = new Entite($this->getSQLQuery(),$id_fournisseur);
		$infoEntite = $entite->getInfo();
		$nomFournisseur = $infoEntite['denomination'];

		$entite = new Entite($this->getSQLQuery(),$this->id_e);
		$infoEntite = $entite->getInfo();
		$nomCol = $infoEntite['denomination'];

		$actionCreator = $this->getActionCreator();
		$actionCreator->addAction($this->id_e,$this->id_u,$this->action,"L'inscription de $nomFournisseur a été refusé");
		$actionCreator->addToEntite($id_fournisseur,"$nomCol a refusé l'inscription");

		$message = "Le fournisseur $nomFournisseur a été refusé par la collectivité $nomCol";
		$this->setLastMessage($message);
		$this->notify($this->action, $this->type, $message);
		return true;
	}
}