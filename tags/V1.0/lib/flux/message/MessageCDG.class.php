<?php

class MessageCDG extends Message {
	
	function getType(){
		return "message_cdg";
	}
	
	public function canCreate($type){
		return $type == Entite::TYPE_CENTRE_DE_GESTION;
	}
	
	public function getTypeDestinataire(){
		return Entite::TYPE_COLLECTIVITE;
	}
	
	public function hasMultipleDestinataire(){
		return false;
	}

	
}