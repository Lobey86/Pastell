<?php
require_once("Message.class.php");

class MessageDevis extends Message {
	
	function getType(){
		return "devis";
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_FOURNISSEUR;
	}
	
	public function getTypeDestinataire(){
		return Entite::TYPE_COLLECTIVITE;
	}
	
}