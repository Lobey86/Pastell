<?php

class MessageArrete extends Message {
	
	const TYPE = "arrete";
	
	//TODO Ca devrait être un const ???
	function getType(){
		return "arrete";
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

	public function  getFormulaire(){
		return "rh-arrete.yml";
	}
	
}