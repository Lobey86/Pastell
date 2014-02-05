<?php

class SAEVerif extends ActionExecutor {
	
	public function go(){
		$sae = $this->getConnecteur('SAE');
		
		$id_transfert = $this->getDonneesFormulaire()->get('sae_transfert_id');
		$sae_config = $this->getConnecteurConfigByType('SAE');
		$ar = $sae->getAcuseReception($id_transfert);
		
		if (! $ar){
			if ($sae->getLastErrorCode() == 7){
				$max_delai_ar = $sae_config->get("max_delai_ar") * 60;
				$lastAction = $this->getDocumentActionEntite()->getLastAction($this->id_e,$this->id_d);
				$time_action = strtotime($lastAction['date']);
				if (time() - $time_action < $max_delai_ar){
					$this->setLastMessage("L'accusé de réception n'est pas encore disponible");
					return false;
				}
			}
			
			$message = $sae->getLastError();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'verif-sae-erreur',$message);	
			$this->notify($this->action, $this->type,$message);										
			return false;
		} 
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		$donneesFormulaire->addFileFromData('ar_sae','ar.xml',$ar);			
		
		$xml = simplexml_load_string($ar);
		$message = utf8_decode(strval($xml->ReplyCode) . " - " . strval($xml->Comment));
		
		$message = "Récupération de l'accusé de réception : $message"; 
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'ar-recu-sae',$message);
		
		$this->setLastMessage($message);
		return true;
	}
}