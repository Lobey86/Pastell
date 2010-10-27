<?php
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentType.class.php");

require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

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
		echo ">> $tedetis_transaction_id - $status";
		if ($status === false){
			echo $tedetis->getLastError();
			return false;
		} 
		
		if ($status != Tedetis::STATUS_ACQUITTEMENT_RECU){
			return false;
		}
			
		$message = "L'acte a été acquitté par le contrôle de légalité";
		$actionCreator->addAction($this->id_e,0,'acquiter-tdt',$message);
		$this->notify('acquiter-tdt', 'rh-actes',$message);
		
		return true;
		//$theAction = $documentTypeFactory->getDocumentType($infoDocument['type'])->getAction();
		//include( dirname(__FILE__) . "/envoyer_au_cdg.php");
	}

}