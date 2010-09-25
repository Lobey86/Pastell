<?php

require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/flux/Flux.class.php");
require_once( PASTELL_PATH . "/lib/flux/message/MessageArrete.class.php");

class FluxActesRH extends Flux {
	
	const TYPE  = "rh-actes";
	
	const STATE_SEND_TDT = "Télétransmis";
	const STATE_SEND_TDT_ACK = "Acquitté";
	const STATE_SEND_CDG = "Envoyé au CDG";
	const STATE_ARCHIVE = "Archivé";
	
	public function getType(){
		return self::TYPE;
	}
	
	public function getFluxTitre(){
		return "Actes RH";
	}
	
	public function getMessageInit(){
		return new MessageArrete();
	}
	
	public function getNextState($state){
		switch($state){
			case Flux::STATE_POSTE : return self::STATE_SEND_TDT;
			case self::STATE_SEND_TDT : return self::STATE_SEND_TDT_ACK;
			case self::STATE_SEND_TDT_ACK : return self::STATE_SEND_CDG;
			case self:: STATE_SEND_CDG : return self::STATE_ARCHIVE;
		}
		return parent::getNextState($state);
	}
	
	public function getAction($type_flux,$etat){
		$result = array();
		if ($etat == 'init'){
			$result = array('poste' => "Envoyer l'acte");
		}
		return $result;
	}
	
	
	public function getFamille(){
		return "Flux RH";
	}
	
	public function canView($entiteType){
		return $entiteType != Entite::TYPE_FOURNISSEUR;
	}
	
}