<?php
require_once(PASTELL_PATH."/lib/connecteur/tedetis/TedetisFactory.class.php");

class TedetisAnnulation  extends ActionExecutor {

	public function go(){
		
		$collectiviteProperties = $this->getCollectiviteProperties();
	
		$tedetis = TedetisFactory::getInstance($collectiviteProperties);

		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		
		if (!  $tedetis->annulationActes($tedetis_transaction_id) ){
			$this->setLastMessage( $tedetis->getLastError());
			return false;
		}
		$message  = "Une notification d'annulation a été envoyé au contrôle de légalité";
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,$message);
			
		
		$this->setLastMessage($message);
		return true;			
	}
}