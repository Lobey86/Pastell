<?php

require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/flux/Flux.class.php");
require_once( PASTELL_PATH . "/lib/flux/message/MessageDemandeDevis.class.php");

class FluxDevis extends Flux {
	
	const TYPE  = "gf_devis";
	
	
	public function getFluxTitre(){
		return "Demande de devis";
	}
	
	public function getMessageInit(){
		return new MessageDemandeDevis();
	}
	
	public function getType(){
		return self::TYPE;
	}
	
	public function getFamille(){
		return "Flux financier";
	}
}