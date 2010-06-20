<?php

require_once("Message.class.php");

class MessageInscriptionRefuser extends Message {
	
	function getType(){
		return "inscription_refuser";
	}
	
	function getDescription(){
		return "En envoyant ce message, vous refuser que le fournisseur puisse poster des documents (Devis, Facture, ...) sur Pastell";
	}
	
	function getLienResponse(){
		return "Refuser le fournisseur";
	}
	
}