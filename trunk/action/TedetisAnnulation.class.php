<?php

class TedetisAnnulation  extends ActionExecutor {

	public function go(){
		
		$tdT = $this->getConnecteur("TdT"); 
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		
		if (!  $tdT->annulationActes($tedetis_transaction_id) ){
			$this->setLastMessage( $tedetis->getLastError());
			return false;
		}
		$message  = "Une notification d'annulation a été envoyé au contrôle de légalité";
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,$message);
			
		
		$this->setLastMessage($message);
		return true;			
	}
}