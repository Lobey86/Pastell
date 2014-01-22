<?php

class TedetisRecupHeliosCG extends ActionExecutor {

	public function go(){		
		$tdT = $this->getConnecteur("TdT"); 
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		$actionCreator = $this->getActionCreator();
		if ( ! $tedetis_transaction_id){
			$actionCreator->addAction($this->id_e,0,'tdt-error',"Une erreur est survenu lors de l'envoie à ".$tedetis->getLogicielName());
			return false;
		}

		$status = $tdT->getStatusHelios($tedetis_transaction_id);
		
		if ($status === false){
			$this->setLastMessage($tdT->getLastError());
			return false;
		} 
		
		$status_info = $tdT->getStatusInfo($status);
		
		$next_message = "La transaction est dans l'état : $status_info ($status) ";
		
		if ($status == TdtConnecteur::STATUS_ACQUITTEMENT_RECU) {
			$next_action = 'acquiter-tdt';
			$next_message = "Un acquittement PES a été recu";
		}
		if ($status == TdtConnecteur::STATUS_REFUSE){
			$next_action = 'refus-tdt';
			$next_message = "Le fichier PES a été refusé";
		}
		if ($status == TdtConnecteur::STATUS_HELIOS_INFO){
			$next_action = 'info-tdt';
			$next_message = "Une réponse est disponible pour ce fichier PES";
		}
		if (in_array($status,array(TdtConnecteur::STATUS_ACQUITTEMENT_RECU,TdtConnecteur::STATUS_REFUSE,TdtConnecteur::STATUS_HELIOS_INFO))){
			$this->getDonneesFormulaire()->setData('has_reponse',true);
			$retour = $tdT->getFichierRetour($tedetis_transaction_id);
			$this->getDonneesFormulaire()->addFileFromData('fichier_reponse', "retour.xml", $retour);
			$actionCreator->addAction($this->id_e,0,$next_action,$next_message);
			$this->notify('acquiter-tdt', 'helios',$next_message);
		}
		$this->setLastMessage( $next_message );
		return true;
	}

}
