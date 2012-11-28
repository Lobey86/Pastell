<?php
require_once( __DIR__ . "/../connecteur/Megalis.class.php");

class MegalisTestDepot extends ActionExecutor {
	
	public function go(){
		$megalis = new Megalis($this->getGlobalProperties(),new SSH2());	
		$entiteInfo =  $this->getEntite()->getInfo();
		
		$authorityInfo = array(
			"sae_id_versant" =>  $this->getCollectiviteProperties()->get("sae_identifiant_versant"),
			"sae_id_archive" =>  $this->getCollectiviteProperties()->get("sae_identifiant_archive"),
			"sae_numero_aggrement" =>  $this->getCollectiviteProperties()->get("sae_numero_agrement"),
			"sae_originating_agency" =>  $this->getCollectiviteProperties()->get("sae_originating_agency"),
			"name" =>  $entiteInfo['denomination'],
			"siren" => $entiteInfo['siren'],
		);
		
		$result = $megalis->createDepot($authorityInfo);
		if (!$result){
			$this->setLastMessage("Erreur lors de la création de l'archive");
			return false;
		}
		$this->setLastMessage("Création de l'enveloppe $result");
		return true;
	}
	
	
}