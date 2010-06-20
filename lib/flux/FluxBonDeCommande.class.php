<?php

require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/flux/Flux.class.php");
require_once( PASTELL_PATH . "/lib/flux/message/MessageBonDeCommande.class.php");

class FluxBonDeCommande extends Flux {
	
	const TYPE  = "gf_bon_de_commande";
	
	
	public function getFluxTitre(){
		return "Bons de commande";
	}
	
	public function getMessageInit(){
		return new MessageBonDeCommande();
	}
	
	public function getType(){
		return self::TYPE;
	}
	
	public function getFamille(){
		return "Flux financier";
	}
	
}