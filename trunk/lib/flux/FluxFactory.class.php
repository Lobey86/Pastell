<?php

require_once("FluxDevis.class.php");
require_once("Flux.class.php");
require_once("FluxInscriptionFournisseur.class.php");
require_once("FluxFacture.class.php");
require_once("FluxArrete.class.php");
require_once("FluxBonDeCommande.class.php");
require_once("FluxContrat.class.php");
require_once("FluxMessageCDG.class.php");


class FluxFactory{
	
	
	public static function getInstance($type_flux){
		switch($type_flux){
			case FluxDevis::TYPE : return new FluxDevis(); break;
			case FluxBonDeCommande::TYPE : return new FluxBonDeCommande(); break;
			case FluxFacture::TYPE : return new FluxFacture(); break;
			case FluxInscriptionFournisseur::TYPE : return new FluxInscriptionFournisseur(); break;
			case FluxArrete::TYPE : return new FluxArrete(); break;
			case FluxContrat::TYPE : return new FluxContrat(); break;
			case FluxMessageCDG::TYPE : return new FluxMessageCDG(); break;
		}	
		$flux = new Flux();
		$flux->setType($type_flux);
		return $flux;
	}
	
	//TODO !!!!
	public static function getTitreS($flux_type){
		$ff = new FluxFactory();
		return $ff->getTitre($flux_type);
	}
	
	public function getTitre($flux_type){
		if (empty($flux_type)){
			return "flux";
		}
		$f = FluxFactory::getFlux();
		foreach($f as $type){
			foreach($type as $nom => $titre){
				if ($nom == $flux_type) return $titre;
			}
		}
		return "Flux";
	}
	
	public static function getFluxByEntite($entite){
		$result = array();
		$theResult = array();
		
		$allFlux = FluxFactory::getAllFlux();
		foreach ($allFlux as $flux_type => $flux_titre){
			$flux = FluxFactory::getInstance($flux_type);
			if ($flux->canView($entite['type'])){
				$result[] = $flux;
			}
		}
		
		foreach($result as $flux){
			$theResult[$flux->getFamille()][$flux->getType()] = $flux->getFluxTitre();
		}
		
		return $theResult;
	}
	
	public static function getAllFlux(){
			return array(
									'inscription_fournisseur' => 'Inscription',
									'message' => 'Message',
 					
									'gf_devis' => 'Demande de devis',
 									'gf_bon_de_commande'=> 'Bons de commande',
									'gf_facture' => 'Factures',
				
									'rh_arrete' => 'Arretés',
									'rh_contrat' => 'Contrats',
									'rh_message_cdg' => 'Message Centre de gestion'
						);
		
	}
	
	//TODO factoriser avec getFluxByEntite
	public static function getFlux(){
		return array(
			'Flux généraux' => array(
									'inscription_fournisseur' => 'Inscription fournisseur',
									'message' => 'Message',
 								),
			'Flux financier' => array(
									'gf_devis' => 'Demande de devis',
 									'gf_bon_de_commande'=> 'Bons de commande',
									'gf_facture' => 'Factures',
								),
			'Flux RH' => array(
									'rh_arrete' => 'Arreté',
									'rh_contrat' => 'Contrat',
									'rh_message_cdg' => 'Message Centre de gestion'
								)
		
		);
	}
}