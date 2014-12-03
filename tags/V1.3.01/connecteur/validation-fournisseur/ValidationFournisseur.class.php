<?php
class ValidationFournisseur extends Connecteur {
	
	private $id_e;
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->id_e = $donneesFormulaire->get('id_e');
	}
	
	public function getIdModerateur(){
		return $this->id_e;
	}
	
}
