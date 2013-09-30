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
        parent::__construct('Le connecteur n\'est pas activ�');
    }
}

abstract class Connecteur {
	
	protected $lastError;
	private $docDonneesFormulaire;

	abstract function setConnecteurConfig(DonneesFormulaire $donneesFormulaire);
		
	public function getLastError(){
		return $this->lastError;
	}
	
	/**
	 * Retourne les donn�es du flux en cours de traitement.
	 * Le connecteur ne doit acc�der qu'aux seuls attributs � sa port�e :
	 * - attributs publics : d�clar�s dans le flux
	 * - attributs priv�s : d�clar�s par le connecteur lui-m�me
	 * Il ne doit pas acc�der aux attributs d�clar�s par d'autres connecteurs.
	 */
	public function getDocDonneesFormulaire() {
		return $this->docDonneesFormulaire;
	}
	
	public function setDocDonneesFormulaire(DonneesFormulaire $docDonneesFormulaire) {
		$this->docDonneesFormulaire = $docDonneesFormulaire;
	}
}