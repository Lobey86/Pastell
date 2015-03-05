<?php 

class FournisseurFactureNotifieRenvoi extends ActionExecutor {
	
	
	public function go(){
		$id_e_fournisseur = $this->getDocumentEntite()->getEntiteWithRole($this->id_d, 'editeur');
		
		$actionCreator = $this->getActionCreator();
		$actionCreator->addAction($this->id_e,$this->id_u,$this->action, "La notification de renvoi a été envoyé au fournisseur");
		$actionCreator->addToEntite($id_e_fournisseur,"La notification de renvoi a été envoyé par la collectivité");
		
		$this->getNotificationMail()->notify($id_e_fournisseur,$this->id_d,$this->action,$this->type, "La facture a été renvoyé par la collectivite");

		$this->setLastMessage("La facture a été renvoyé au fournisseur");
		return true;
	}
	
}