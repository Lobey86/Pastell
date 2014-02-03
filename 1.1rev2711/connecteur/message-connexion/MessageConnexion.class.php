<?php 

class MessageConnexion extends Connecteur {
	
	private $donneesFormulaire;
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire) {
		$this->donneesFormulaire = $donneesFormulaire;
	}
	
	public function getMessage(){
		$now = date("Y-m-d");
		if ($this->donneesFormulaire->get('debut') > $now){
			return false;
		}
		if ($this->donneesFormulaire->get('fin') < $now){
			return false;
		}
		return $this->donneesFormulaire->get('message');
	}
	
}