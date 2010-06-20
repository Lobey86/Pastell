<?php

require_once( PASTELL_PATH . "/lib/flux/message/MessageFacture.class.php");

class FluxFacture extends Flux {
	
	const TYPE  = "gf_facture";
	
	public function getFluxTitre(){
		return "Factures";
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_FOURNISSEUR;
	}
	
	public function getMessageInit(){
		return new MessageFacture();
	}
	
	public function getType(){
		return self::TYPE;
	}
	
	public function getFamille(){
		return "Flux financier";
	}
}