<?php
require_once (PASTELL_PATH . "/lib/document/DocumentType.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/action/EnvoieCDG.class.php");

class TedetisRecupHelios extends ActionExecutor {

	public function go(){
		
		$tedetis = TedetisFactory::getInstance($this->getCollectiviteProperties());	
		
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		$actionCreator = $this->getActionCreator();
		if ( ! $tedetis_transaction_id){
			$actionCreator->addAction($this->id_e,0,'tdt-error',"Une erreur est survenu lors de l'envoie à ".$tedetis->getLogicielName());
			return false;
		}
			
		
	
		$status = $tedetis->getStatusHelios($tedetis_transaction_id);
		
		if ($status === false){
			$this->setLastMessage($tedetis->getLastError());
			return false;
		} 
		
		$status_info = $tedetis->getStatusInfo($status);
		
		$next_message = "La transaction est dans l'état : $status_info ($status) ";
		
		if ($status == Tedetis::STATUS_ACQUITTEMENT_RECU) {
			$next_action = 'acquiter-tdt';
			$next_message = "Un acquittement PES a été recu";
		}
		if ($status == Tedetis::STATUS_REFUSE){
			$next_action = 'refus-tdt';
			$next_message = "Le fichier PES a été refusé";
		}
		if ($status == Tedetis::STATUS_HELIOS_INFO){
			$next_action = 'info-tdt';
			$next_message = "Une réponse est disponible pour ce fichier PES";
		}
		
		if (in_array($status,array(Tedetis::STATUS_ACQUITTEMENT_RECU,Tedetis::STATUS_REFUSE,Tedetis::STATUS_HELIOS_INFO))){
			$this->getDonneesFormulaire()->setData('has_reponse',true);
			$retour = $tedetis->getFichierRetour($tedetis_transaction_id);
			$this->getDonneesFormulaire()->addFileFromData('fichier_reponse', "retour.xml", $retour);
			$actionCreator->addAction($this->id_e,0,$next_action,$next_message);
			$this->notify('acquiter-tdt', 'helios',$next_message);
		}
		$this->setLastMessage( $next_message );
		return true;
	}

}
