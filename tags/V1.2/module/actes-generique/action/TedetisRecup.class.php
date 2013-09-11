<?php

class TedetisRecup extends ActionExecutor {

	public function go(){
		$tdT = $this->getConnecteur("TdT"); 
		
		if (!$tdT){
			throw new Exception("Aucun Tdt disponible");
		}
		
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
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
			$this->getNotificationMail()->notify($this->id_e,$this->id_d,$this->action, $this->type,$message);													
			return false;
		} 
		
		if ($status != TdtConnecteur::STATUS_ACQUITTEMENT_RECU){
			$this->setLastMessage("La transaction a comme statut : " . TdtConnecteur::getStatusString($status));
			return true;
		}
		
		$aractes = $tdT->getARActes();
		
		$actionCreator->addAction($this->id_e,0,'acquiter-tdt',"L'acte a été acquitté par le contrôle de légalité");
		
		$infoDocument = $this->getDocument()->getInfo($this->id_d);
		$documentActionEntite = $this->getDocumentActionEntite();
		$infoUser = $documentActionEntite->getUserFromAction($this->id_e,$this->id_d,'send-tdt');
		$message = "L'acte {$infoDocument['titre']} télétransmis par {$infoUser['prenom']} {$infoUser['nom']} a été acquitté par le contrôle de légalité";
		
		$message .= "\n\nConsulter le détail de l'acte : " . SITE_BASE . "document/detail.php?id_d={$this->id_d}&id_e={$this->id_e}";
		
		$bordereau_data = $tdT->getBordereau($tedetis_transaction_id);
		
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		if ($bordereau_data){
			$donneesFormulaire->setData('has_bordereau',true);
			$donneesFormulaire->addFileFromData('bordereau', $infoDocument['titre']."-bordereau.pdf",$bordereau_data);
		}
		if ($aractes){
			$donneesFormulaire->addFileFromData('aractes', "ARActes.xml",$aractes);
		}
		
		$donneesFormulaire->setData('date_ar', $tdT->getDateAR($tedetis_transaction_id));
		
		$this->notify('acquiter-tdt', 'actes',$message);
	
		$this->setLastMessage("L'acquittement du contrôle de légalité a été reçu.");
		return true;

	}

}
