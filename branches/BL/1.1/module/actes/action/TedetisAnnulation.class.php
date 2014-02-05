<?php

class TedetisAnnulation  extends ActionExecutor {

	public function go(){
		
		$tdT = $this->getConnecteur("TdT"); 
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		$id_annulation_transaction = $tdT->annulationActes($tedetis_transaction_id); 
		if (!  $id_annulation_transaction ){
			$this->setLastMessage( $tedetis->getLastError());
			return false;
		}
		$this->getDonneesFormulaire()->setData('tedetis_annulation_id',$id_annulation_transaction);
		$this->getDonneesFormulaire()->setData('has_annulation',true);
		
		$message  = "Une notification d'annulation a été envoyé au contrôle de légalité";
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,$message);
			
		
		$this->setLastMessage($message);
		return true;			
	}
}