<?php

require_once("Message.class.php");

class MessageBonDeCommande extends Message {
	
	function getType(){
		return "bon_de_commande";
	}
	
	public function getMessageReponse(){
		return array("bon_de_livraison");
	}
}