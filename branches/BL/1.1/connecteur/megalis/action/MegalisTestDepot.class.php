<?php

class MegalisTestDepot extends ActionExecutor {
	
	public function go(){
		$megalis = $this->getGlobalConnecteur('Megalis');
		
		$sae_config = $this->getConnecteurConfigByType('SAE');
		
		
		$entiteInfo =  $this->getEntite()->getInfo();
		
		$authorityInfo = array(
			"sae_id_versant" =>  $sae_config->get("sae_identifiant_versant"),
			"sae_id_archive" =>  $sae_config->get("sae_identifiant_archive"),
			"sae_numero_aggrement" =>  $sae_config->get("sae_numero_agrement"),
			"sae_originating_agency" =>  $sae_config->get("sae_originating_agency"),
			"name" =>  $entiteInfo['denomination'],
			"siren" => $entiteInfo['siren'],
		);
		
		if (!$megalis){
			$this->setLastMessage("Impossible de trouver le connecteur Mégalis global");
			return false;
		}
		
		$result = $megalis->createDepot($authorityInfo);
		if (!$result){
			$this->setLastMessage("Erreur lors de la création de l'archive");
			return false;
		}
		$this->setLastMessage("Création de l'enveloppe $result");
		return true;
	}
	
	
}