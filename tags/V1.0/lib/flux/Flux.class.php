<?php

//TODO ref circulaire
require_once("FluxFactory.class.php");

class Flux {
	
	const STATE_INIT = 'init';
	const STATE_POSTE = 'poste';
	
	//TODO provisoire
	public function setType($type){
		$this->type = $type;
	}
	
	public function getType(){
		return $this->type;
	}
	
	//TODO c'est dans message...
	public function getAction($type_flux,$etat){
		$result = array();
		if ($type_flux == FluxInscriptionFournisseur::TYPE && $etat == 'poste'){
			$result = array('accepter' => "Accepter l'inscription", 'refuser'=>"Refuser l'inscription");
		}
		return $result;
	}
	
	public function canCreate($typeEntite){
		return $typeEntite == Entite::TYPE_COLLECTIVITE;
	}
	
	public function getMessageInit(){
		return new Message();
	}
	
	public function getFluxTitre(){
		return "Message";
	}
	
	public function getNextState($state){
		if ($state == self::STATE_INIT){
			return self::STATE_POSTE;
		}	
	}
	
	public function canView($entiteType){
		return true;
	}
	
	public function getFamille(){
		return "Flux généraux";
	}
	
}