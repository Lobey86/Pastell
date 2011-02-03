<?php
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentType.class.php");

require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

require_once( PASTELL_PATH . "/action/EnvoieCDG.class.php");

class TedetisRecup extends ActionExecutor {

	public function go(){

			
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		$actionCreator = $this->getActionCreator();
		if ( ! $tedetis_transaction_id){
			$actionCreator->addAction($this->id_e,0,'tdt-error',"Une erreur est survenu lors de l'envoie au Tedetis");
			return false;
		}
			
		$tedetis = new Tedetis($this->getCollectiviteProperties());
	
		$status = $tedetis->getStatus($tedetis_transaction_id);
		
		if ($status === false){
			$this->setLastMessage($tedetis->getLastError());
			return false;
		} 
		
		if ($status != Tedetis::STATUS_ACQUITTEMENT_RECU){
			$this->setLastMessage("La transaction à comme status : " . Tedetis::getStatusString($status));
			return true;
		}
			
		$message = "L'acte a été acquitté par le contrôle de légalité";
		$actionCreator->addAction($this->id_e,0,'acquiter-tdt',$message);
		$this->notify('acquiter-tdt', 'rh-actes',$message);
		
		if ($this->getDonneesFormulaire()->get('envoi_cdg')) {
			$envoieCDG = new EnvoieCDG($this->getZLog(), $this->getSQLQuery(),$this->id_d,$this->id_e,0,$this->type);
			$envoieCDG->setNotificationMail($this->getNotificationMail());
			$envoieCDG->go();
		}
		
		$this->setLastMessage("L'acquittement du contrôle de légalité a été reçu.");
		return true;

	}

}