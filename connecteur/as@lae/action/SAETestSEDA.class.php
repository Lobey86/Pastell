<?php


class SAETestSEDA extends ActionExecutor {
	
	public function go(){
		$sae = $this->getMyConnecteur();
		
		$donneesFormulaire = $this->getConnecteurProperties();
		
		$bordereau = file_get_contents($donneesFormulaire->getFilePath('sae_bordereau_test'));
				
		$result = $sae->sendArchive($bordereau,$donneesFormulaire->get('sae_archive_test'));
		
		if (! $result){
			$this->setLastMessage("Le test a échoué : " . $sae->getLastError());
			return false;
		} else {			
			$this->setLastMessage("Envoie de la transaction  au SAE ({$authorityInfo['sae_wsdl']})");
			return true;
		}
	}
	
}