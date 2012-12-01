<?php
require_once( PASTELL_PATH . "/lib/system/Asalae.class.php");

class SAEVerifMegalis extends ActionExecutor {
	
	public function go(){
		
		$collectiviteProperties = $this->getCollectiviteProperties();		
		if (!$collectiviteProperties){
			return false;
		}
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
		$ar = $asalae->getAcuseReception($id_transfert);
		if (! $ar){
			if ($asalae->getLastErrorCode() == 7){
				$max_delai_ar = $collectiviteProperties->get("max_delai_ar") * 60;
				$lastAction = $this->getDocumentActionEntite()->getLastAction($this->id_e,$this->id_d);
				$time_action = strtotime($lastAction['date']);
				if (time() - $time_action < $max_delai_ar){
					$this->setLastMessage("L'accusé de réception n'est pas encore disponible");
					return false;
				}
			}
			
			$message = $asalae->getLastError();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'verif-sae-erreur',$message);	
			$this->getNotificationMail()->notify($this->id_e,$this->id_d,$this->action, $this->type,$message);										
			return false;
		} 
		
		$donneesFormulaire = $this->getDonneesFormulaireFactory()->get($this->id_d,'megalis');
		$donneesFormulaire->addFileFromData('ar_sae','ar.xml',$ar);			
		
		$xml = simplexml_load_string($ar);
		$message = utf8_decode(strval($xml->ReplyCode) . " - " . strval($xml->Comment));
		
		$message = "Récupération de l'accusé de réception : $message"; 
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'ar-recu-sae',$message);
		
		$this->setLastMessage($message);
		return true;
	}
}