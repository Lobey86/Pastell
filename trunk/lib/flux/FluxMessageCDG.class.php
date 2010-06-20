<?php

require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/flux/Flux.class.php");
require_once( PASTELL_PATH . "/lib/flux/message/MessageArrete.class.php");

class FluxMessageCDG extends Flux {
	
	const TYPE  = "rh_message_cdg";
	
	
	public function getFluxTitre(){
		return "Message centre de gestion";
	}
	
	public function getMessageInit(){
		return new MessageCDG();
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_CENTRE_DE_GESTION;
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