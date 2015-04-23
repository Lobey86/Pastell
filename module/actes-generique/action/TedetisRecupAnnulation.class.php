<?php

class TedetisRecupAnnulation extends ActionExecutor {

	public function go(){
		$tdT = $this->getConnecteur("TdT"); 
		
		if (!$tdT){
			throw new Exception("Aucun Tdt disponible");
		}
		
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_annulation_id');
		
		$actionCreator = $this->getActionCreator();
		if ( ! $tedetis_transaction_id){
			$actionCreator->addAction($this->id_e,0,'tdt-error',"Une erreur est survenu lors de l'envoie à ".$tdT->getLogicielName());
			return false;
		}
			
		try {
			$status = $tdT->getStatus($tedetis_transaction_id);
		} catch (Exception $e) {
			$message = "Echec de la récupération des informations : " .  $e->getMessage();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'erreur-verif-tdt',$message);		
			$this->notify($this->action, $this->type,$message);													
			return false;
		} 
		if ($status != TdtConnecteur::STATUS_ACQUITTEMENT_RECU){
			$this->setLastMessage("La transaction d'annulation a comme statut : " . TdtConnecteur::getStatusString($status));
			return true;
		}
		$actionCreator->addAction($this->id_e,0,'annuler-tdt',"L'acte a été annulé par le contrôle de légalité");
		
		$this->getDonneesFormulaire()->setData('date_ar_annulation', $tdT->getDateAR($tedetis_transaction_id));
		
		$message = "L'acquittement pour l'annulation de l'acte a été reçu.";
		$this->notify('annuler-tdt', $this->type,$message);
		$this->setLastMessage($message);
		return true;

	}

}
