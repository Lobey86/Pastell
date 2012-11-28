<?php
require_once( PASTELL_PATH . "/lib/system/Asalae.class.php");

class SAEValidationMegalis extends ActionExecutor {
	
	public function go(){
		
		$collectiviteProperties = $this->getCollectiviteProperties();		
		$authorityInfo = array(
							"sae_wsdl" =>  $collectiviteProperties->get("sae_wsdl"),
							"sae_login" =>  $collectiviteProperties->get("sae_login"),
							"sae_password" =>  $collectiviteProperties->get("sae_password"),
							"sae_numero_aggrement" =>  $collectiviteProperties->get("sae_numero_agrement"),				
		);
			
		
		$id_transfert = $this->getDonneesFormulaire()->get('transfert_id');
		if (!$id_transfert){
			$message = "Impossible de trouver l'identifiant du transfert";
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'erreur-envoie-sae',$message);		
			$this->getNotificationMail()->notify($this->id_e,$this->id_d,$this->action, $this->type,$message);													
			return false;
		}
		
		$asalae = new Asalae($authorityInfo);
		
		
		$validation = $asalae->getReply($id_transfert);
		
		if (! $validation){
			if ($asalae->getLastErrorCode() == 8){
				$max_delai_ar = $collectiviteProperties->get("max_delai_validation") * 24 * 60 * 60;
				$lastAction = $this->getDocumentActionEntite()->getLastAction($this->id_e,$this->id_d);
				$time_action = strtotime($lastAction['date']);
				if (time() - $time_action < $max_delai_ar){
					$this->setLastMessage("Le document n'a pas encore été traité");
					return false;
				}
			}
			
			$message = $asalae->getLastError();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'validation-sae-erreur',$message);	
			$this->getNotificationMail()->notify($this->id_e,$this->id_d,$this->action, $this->type,$message);										
			return false;
		} 
		
		$donneesFormulaire = $this->getDonneesFormulaireFactory()->get($this->id_d,'megalis');
		$donneesFormulaire->addFileFromData('reply_sae','reply.xml',$validation);			
		
		$xml = simplexml_load_string($validation);
		$message = utf8_decode(strval($xml->ReplyCode) . " - " . strval($xml->Comment));
		
		$nodeName = strval($xml->getName());
		if ($nodeName == 'ArchiveTransferAcceptance'){
			$url = $asalae->getURL(strval($xml->Archive->ArchivalAgencyArchiveIdentifier));
			$donneesFormulaire->setData('url_archive', $url);
			$message = "La transaction a été acceptée par le SAE";
			$next_action = "accepter-sae";			
			
		} else {
			$message = "La transaction a été refusée par le SAE";
			$next_action = "rejet-sae";		
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$next_action,$message);	
		$this->getNotificationMail()->notify($this->id_e,$this->id_d,$next_action, $this->type,$message);				
		
		$this->setLastMessage($message);
		return true;
	}
}