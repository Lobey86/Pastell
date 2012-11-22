<?php

class Message {
	
	public function canCreate($type){
		return $type == Entite::TYPE_COLLECTIVITE;
	}
	
	public function getTypeDestinataire(){
		return Entite::TYPE_FOURNISSEUR;
	}
	
	public function hasMultipleDestinataire(){
		return false;
	}
	
	public function getMessageReponse(){
		return array();
	}
	
	public function  getFormulaire(){
		return false;
	}
	
	public function getType(){
		return "message";
	}
	
	public function getDescription(){
		return false;
	}
	
	public function getLienResponse(){
		return "Répondre";
	}

}