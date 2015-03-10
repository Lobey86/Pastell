<?php
class FournisseurFactureNotifieLiquidation extends ActionExecutor {
	
	public function go(){
		$id_e_fournisseur = $this->getDocumentEntite()->getEntiteWithRole($this->id_d, 'editeur');
		$actionCreator = $this->getActionCreator();
		$actionCreator->addAction($this->id_e,$this->id_u,$this->action, "La notification de la liquidation a été envoyé au fournisseur");
		$actionCreator->addToEntite($id_e_fournisseur,"La notification de liquidation a été envoyé par la collectivité");
		$this->getNotificationMail()->notify($id_e_fournisseur,$this->id_d,$this->action,$this->type, "La facture a été liquider par la collectivite");
		$this->setLastMessage("La notification de la liquidation a été envoyé au fournisseur");
		return true;
	}
	
}