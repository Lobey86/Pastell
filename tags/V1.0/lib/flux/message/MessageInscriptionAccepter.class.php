<?php

require_once("Message.class.php");

class MessageInscriptionAccepter extends Message {
	
	function getType(){
		return "inscription_accepter";
	}
	
	function getDescription(){
		return "En envoyant ce message, vous accepter que le fournisseur puisse poster des documents (Devis, Facture, ...) sur Pastell";
	}
	
	function getLienResponse(){
		return "Accepter le fournisseur";
	}
	
}