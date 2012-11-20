<?php

require_once("Message.class.php");

class MessageBonDeLivraison extends Message {
	
	function getType(){
		return "bon_de_livraison";
	}
	
	public function getTypeDestinataire(){
		return Entite::TYPE_FOURNISSEUR;
	}

}