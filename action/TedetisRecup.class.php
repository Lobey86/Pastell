<?php
require_once (PASTELL_PATH . "/lib/document/DocumentType.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/action/EnvoieCDG.class.php");

class TedetisRecup extends ActionExecutor {

	public function go(){

		$tedetis = TedetisFactory::getInstance($this->getCollectiviteProperties());	
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		$actionCreator = $this->getActionCreator();
		if ( ! $tedetis_transaction_id){
			$actionCreator->addAction($this->id_e,0,'tdt-error',"Une erreur est survenu lors de l'envoie à ".$tedetis->getLogicielName());
			return false;
		}
			
			
		$status = $tedetis->getStatus($tedetis_transaction_id);
		
		if ($status === false){
			$this->setLastMessage($tedetis->getLastError());
			return false;
		} 
		
		if ($status != Tedetis::STATUS_ACQUITTEMENT_RECU){
			$this->setLastMessage("La transaction a comme statut : " . Tedetis::getStatusString($status));
			return true;
		}
		
		$aractes = $tedetis->getARActes();
		
		$actionCreator->addAction($this->id_e,0,'acquiter-tdt',"L'acte a été acquitté par le contrôle de légalité");
		
		$infoDocument = $this->getDocument()->getInfo($this->id_d);
		$documentActionEntite = new DocumentActionEntite($this->getSQLQuery());
		$infoUser = $documentActionEntite->getUserFromAction($this->id_e,$this->id_d,'send-tdt');
		$message = "L'acte {$infoDocument['titre']} télétransmis par {$infoUser['prenom']} {$infoUser['nom']} a été acquitté par le contrôle de légalité";
		
		$message .= "\n\nConsulter le détail de l'acte : " . SITE_BASE . "document/detail.php?id_d={$this->id_d}&id_e={$this->id_e}";
		
		$bordereau_data = $tedetis->getBordereau($tedetis_transaction_id);
		
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		if ($bordereau_data){
			$donneesFormulaire->setData('has_bordereau',true);
			$donneesFormulaire->addFileFromData('bordereau', $infoDocument['titre']."-bordereau.pdf",$bordereau_data);
		}
		if ($aractes){
			$donneesFormulaire->addFileFromData('aractes', "ARActes.xml",$aractes);
		}
		
		
		$this->notify('acquiter-tdt', 'actes',$message);
		
		if ($this->getDonneesFormulaire()->get('envoi_cdg')) {
			$envoieCDG = new EnvoieCDG($this->getZLog(), $this->getSQLQuery(),$this->id_d,$this->id_e,0,$this->type);
			$envoieCDG->setNotificationMail($this->getNotificationMail());
			$envoieCDG->go();
		}
		$donneesFormulaire->setData('date_ar', $tedetis->getDateAR($tedetis_transaction_id));
	
		$this->setLastMessage("L'acquittement du contrôle de légalité a été reçu.");
		return true;

	}

}
