<?php
class FakeSAE extends Connecteur {
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		
	}
	
	public function sendArchive(){
		return true;
	}
}