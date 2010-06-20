<?php

require_once("Message.class.php");

class MessageDemandeDevis extends Message {
	
	function getType(){
		return "demande_devis";
	}
	
	public function hasMultipleDestinataire(){
		return true;
	}
	
	public function getMessageReponse(){
		return array("devis");
	}
	
}