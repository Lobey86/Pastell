<?php

require_once( PASTELL_PATH . "/action/Envoyer.class.php");
require_once(PASTELL_PATH . "/lib/system/WebGFC.class.php");

class EnvoyerWebGFC extends Envoyer {
	
	public function go(){
		
		$formulaire = $this->getDonneesFormulaire();
		$messageSousTypeId = $formulaire->get("messageSousTypeId");
		$entite = $this->getEntite();
		$infoEntite = $entite->getInfo();
		$contact = $infoEntite['denomination'];
		$username = $infoEntite['denomination'];
		$titre = $formulaire->get("sujet");
		$object  = $formulaire->get("message");
		if ($formulaire->getFilePath("pj")){
			$fichier = base64_encode(file_get_contents($formulaire->getFilePath("pj")));
		} else {
			$fichier = "";
		}
		$webGFC = new WebGFC();
		$courierID = $webGFC->createCourrier($messageSousTypeId,$contact,$titre,$object,$fichier,$username);
		$this->setLastMessage($webGFC->getLastMessage());
		if (! $courierID){
			return false;
		}
		$formulaire->setData("webgfc_courrier_id", $courierID);
		
		if ( ! parent::go()) {
			return false;
		}
		$this->setLastMessage($webGFC->getLastMessage());
		return true;
	}
	
}