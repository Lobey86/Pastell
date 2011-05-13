<?php

require_once("BordereauSEDA.class.php");
//mail : pastell@sigmalis.com AisohM6D

class Asalae {
	
	private $lastError;
	
	private $collectiviteProperties;
	private $WDSL;
	private $login;
	private $password;
	private $identifiantVersant;
	private $originatingAgency;
	private $identifiantArchive;
	private $numeroAgrement;
	
	public function __construct(DonneesFormulaire $collectiviteProperties){
		$this->collectiviteProperties = $collectiviteProperties;
		$this->WSDL = $collectiviteProperties->get('sae_wsdl');
		$this->login = $collectiviteProperties->get('sae_login');
		$this->password = $collectiviteProperties->get('sae_password');
		$this->identifiantVersant = $collectiviteProperties->get('sae_identifiant_versant');
		$this->identifiantArchive = $collectiviteProperties->get('sae_identifiant_archive');
		$this->numeroAgrement = $collectiviteProperties->get('sae_numero_agrement');
		$this->originatingAgency = $collectiviteProperties->get('sae_originating_agency');
	}

	public function getLastError(){
		return $this->lastError;
	}
	
	public function sendArchive($filename,$content,$description){
		$seda = base64_encode($this->generateSEDA($description));
		if (! $seda){
			return false;
		}
		$client = new SoapClient($this->WSDL);

		$document_content  = base64_encode(file_get_contents("/home/eric/archive.tar.gz"));
		
		$retour  = $client->__soapCall("wsDepot", array("bordereau.xml", $seda, "archive.tar.gz", $document_content, "TARGZ",$this->login,$this->password));
		if ($retour == 0){
			return true;
		}
		
		$this->lastError = "Erreur lors du dépot : le service d'archive a retourné :  $retour";
		return false;
	}
	

	public function generateSEDA($description){
	
		$objet_archive = "Test génération SEDA";
		$provenance_archive = $this->identifiantVersant;
		$identifiant_producteur = $this->identifiantArchive; 
		
		$bordereauSEDA = new BordereauSEDA($this->identifiantVersant, $this->identifiantArchive,$this->numeroAgrement,$this->originatingAgency);
		
		$bordereau = $bordereauSEDA->getBordereau($objet_archive,$description);

		$client = new SoapClient($this->WSDL);

		$seda = $client->wsGSeda($bordereau,$this->login,$this->password);
		if (intval($seda) ){			
			$this->lastError = "Erreur lors de la génération du bordereau : " . $this->getErrorString($seda);
			return false;
		}
		return $seda;
	}
	
	public function getErrorString($number){
		$error = array("connexion réussie","identifiant de connexion inconnu","mot de passe incorrect","connecteur non actif");
		if (empty($error[$number])){
			return "erreur As@lae inconnu ($number)";
		}
		return $error[$number];
	}

}
