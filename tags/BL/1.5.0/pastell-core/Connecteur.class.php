<?php

class ConnecteurException extends Exception {}

class ConnecteurAccesException extends ConnecteurException {
    private $connecteur;
    
    public function __construct(ConnecteurSuspensionIntf $connecteur, $message) {
        parent::__construct($message);
        $this->connecteur = $connecteur;
    }
    
    public function getConnecteur() {
        return $this->connecteur;
    }
}

class ConnecteurActivationException extends ConnecteurException {
    public function __construct() {
        parent::__construct('Le connecteur n\'est pas activé');
    }
}

class ConnecteurSuspensionException extends ConnecteurException {
    public function __construct() {
        parent::__construct('Le connecteur a suspendu les accès au service suite à des erreurs d\'accès répétées. Vérifiez l\'état du service et/ou la configuration d\'accès. La suspension sera levée par un test de connexion réussi.');
    }
}

abstract class Connecteur {
	
	protected $lastError;
    /**
     * @var DonneesFormulaire
     */
	private $docDonneesFormulaire;
    private $connecteur_info;

	abstract function setConnecteurConfig(DonneesFormulaire $donneesFormulaire);
		
	public function getLastError(){
		return $this->lastError;
	}
	
	/**
	 * Retourne les données du flux en cours de traitement.
	 * Le connecteur ne doit accéder qu'aux seuls attributs à sa portée :
	 * - attributs publics : déclarés dans le flux
	 * - attributs privés : déclarés par le connecteur lui-même
	 * Il ne doit pas accéder aux attributs déclarés par d'autres connecteurs.
	 */
	public function getDocDonneesFormulaire() {
		return $this->docDonneesFormulaire;
	}

	public function setDocDonneesFormulaire(DonneesFormulaire $docDonneesFormulaire) {
		$this->docDonneesFormulaire = $docDonneesFormulaire;
	}

    /**
     * Retourne les informations du connecteur entité.
     * @return array
     */
    public function getConnecteurInfo() {
        return $this->connecteur_info;
    }

    public function setConnecteurInfo(array $connecteur_info) {
        $this->connecteur_info = $connecteur_info;
    }

}
