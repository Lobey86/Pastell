<?php

require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/flux/Flux.class.php");
require_once( PASTELL_PATH . "/lib/flux/message/MessageContrat.class.php");

class FluxContrat extends Flux {
	
	const TYPE  = "rh_contrat";
	
	
	public function getFluxTitre(){
		return "Contrat";
	}
	
	public function getMessageInit(){
		return new MessageContrat();
	}
	
	public function getType(){
		return self::TYPE;
	}
	
	public function getFamille(){
		return "Flux RH";
	}
	
	public function canView($entiteType){
		return $entiteType != Entite::TYPE_FOURNISSEUR;
	}
	
}