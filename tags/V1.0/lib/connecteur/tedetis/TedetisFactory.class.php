<?php

require_once( PASTELL_PATH . "/lib/connecteur/tedetis/Tedetis.class.php");
require_once( PASTELL_PATH . "/lib/connecteur/tedetis/S2low.class.php");
require_once( PASTELL_PATH . "/lib/connecteur/tedetis/Stela.class.php");

class TedetisFactory {
	
	
	public static function getInstance(DonneesFormulaire $collectiviteProperties){
		$tdt_type = $collectiviteProperties->get('tdt_type');
		switch($tdt_type){
			case 'stela' : return new Stela($collectiviteProperties);
			case 's2low': 
			default: return new S2low($collectiviteProperties);
		}
		
		
	}
	
}