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
		$reponse = $webGFC->createCourrier($messageSousTypeId,$contact,$titre,$object,$fichier,$username);
		
		if ( ! parent::go()) {
			return false;
		}
	
		
	}
	
}