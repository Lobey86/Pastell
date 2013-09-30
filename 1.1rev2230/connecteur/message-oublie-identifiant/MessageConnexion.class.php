<?php 

class MessageOublieIdentifiant extends Connecteur {
	
	private $donneesFormulaire;
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire) {
		$this->donneesFormulaire = $donneesFormulaire;
	}
	
}