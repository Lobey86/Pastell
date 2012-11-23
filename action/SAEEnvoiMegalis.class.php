<?php

require_once( PASTELL_PATH . "/lib/system/Asalae.class.php");

class SAEEnvoiMegalis extends ActionExecutor {
	
	public function go(){
		
		$bordereau = $this->getDonneesFormulaire()->getFileContent('bordereau');

		if (! $bordereau){
			$message = $this->getDonneesFormulaire()->getLastError();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'erreur-envoie-sae',$message);	
		}
		
		$archive_path = $this->getDonneesFormulaire()->getFilePath('fichier_attache');

		$collectiviteProperties = $this->getCollectiviteProperties();		
		$authorityInfo = array(
							"sae_wsdl" =>  $collectiviteProperties->get("sae_wsdl"),
							"sae_login" =>  $collectiviteProperties->get("sae_login"),
							"sae_password" =>  $collectiviteProperties->get("sae_password"),
							"sae_numero_aggrement" =>  $collectiviteProperties->get("sae_numero_agrement"),				
		);
			
		$asalae = new Asalae($authorityInfo);		
		$result = $asalae->sendArchive($bordereau,$archive_path,"ZIP");
		
		if (! $result){
			$message = "L'envoi de l'archive a échoué : " . $asalae->getLastError();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'erreur-envoie-sae',$message);				
			return false;
		} 
		
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au SAE");
		
		$this->setLastMessage("La transaction à été envoyé au SAE ({$authorityInfo['sae_wsdl']})");
		return true;	
	}
	
	
	
}