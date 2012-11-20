<?php

require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/flux/Flux.class.php");

class FluxInscriptionFournisseur  extends Flux {
	
	const TYPE = "inscription_fournisseur";
	
	const STATE_ACCEPT = "accepter";
	const STATE_REFUS = "refuser";
	
	public function getFluxTitre(){
		return "Inscription fournisseur";
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_FOURNISSEUR;
	}
	
	
	public function getType(){
		return self::TYPE;
	}
}