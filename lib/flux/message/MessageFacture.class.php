<?php

require_once("Message.class.php");

class MessageFacture extends Message {
	
	public function getType(){
		return "facture";
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_FOURNISSEUR;
	}
	
	public function getTypeDestinataire(){
		return Entite::TYPE_COLLECTIVITE;
	}
	

	
}