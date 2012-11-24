<?php
require_once( PASTELL_PATH . "/lib/system/Asalae.class.php");

class SAEVerifMegalis extends ActionExecutor {
	
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
		$ar = $asalae->getAcuseReception($id_transfert);
		if (! $ar){
			$message = $asalae->getLastError();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'verif-sae-erreur',$message);	
			$this->getNotificationMail()->notify($this->id_e,$this->id_d,$this->action, $this->type,$message);										
			return false;
		} 
		
		$this->setLastMessage("Not implemented");
		return false;
	}
}