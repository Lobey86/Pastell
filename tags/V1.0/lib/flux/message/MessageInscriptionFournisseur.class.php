<?php

require_once("Message.class.php");

class MessageInscriptionFournisseur extends Message {
	
	public function getType(){
		return "inscription_fournisseur";
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_FOURNISSEUR;
	}
	
	public function getTypeDestinataire(){
		return Entite::TYPE_COLLECTIVITE;
	}
	
	public function getMessageReponse(){
		return array("inscription_accepter","inscription_refuser");
	}

	
}