<?php

require_once( PASTELL_PATH . "/lib/system/Asalae.class.php");

class SAETestSEDA extends ActionExecutor {
	
	public function go(){
		
		$donneesFormulaire = $this->getConnecteurProperties();
		
		$authorityInfo = array(
					"sae_wsdl" =>  $donneesFormulaire->get("sae_wsdl"),
					"sae_login" =>  $donneesFormulaire->get("sae_login"),
					"sae_password" =>  $donneesFormulaire->get("sae_password"),
					"sae_numero_aggrement" =>  $donneesFormulaire->get("sae_numero_agrement"),				
		);
			
		$asalae = new Asalae($authorityInfo);

		$bordereau = file_get_contents($donneesFormulaire->getFilePath('sae_bordereau_test'));
				
		$result = $asalae->sendArchive($bordereau,$donneesFormulaire->get('sae_archive_test'));
		
		if (! $result){
			$this->setLastMessage("Le test a échoué : " . $asalae->getLastError());
			return false;
		} else {			
			$this->setLastMessage("Envoie de la transaction  au SAE ({$authorityInfo['sae_wsdl']})");
			return true;
		}
	}
	
}