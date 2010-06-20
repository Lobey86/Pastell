<?php

class MessageContrat extends Message {
	
	function getType(){
		return "contrat";
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_COLLECTIVITE;
	}
	
	public function getTypeDestinataire(){
		return Entite::TYPE_CENTRE_DE_GESTION;
	}
	
	public function hasMultipleDestinataire(){
		return false;
	}
	
	
	
}