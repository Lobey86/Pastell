<?php
require_once(__DIR__."/TdtConnecteur.class.php");

class TdtAdapter extends TdtConnecteur {
	
	public function setConnecteurConfig(DonneesFormulaire $donnesFormulaire){}
	
	public function getLogicielName(){
		return "TdtAdapter";
	}
	
	public function testConnexion(){
		throw new Exception("Not implemented");
	}
	
	public function getClassification(){
		throw new Exception("Not implemented");
	}
	
	public function demandeClassification(){
		throw new Exception("Not implemented");
	}
	
	public function annulationActes($id_transaction){
		throw new Exception("Not implemented");
	}
	
	public function verifClassif(){
		throw new Exception("Not implemented");
	}
	
	public function postHelios(DonneesFormulaire $donneesFormulaire){
		throw new Exception("Not implemented");
	}
	
	public function postActes(DonneesFormulaire $donneesFormulaire){
		throw new Exception("Not implemented");
	}
	
	public function getStatusHelios($id_transaction){
		throw new Exception("Not implemented");
	}
	
	public function getStatus($id_transaction){
		throw new Exception("Not implemented");
	}
	
	public function getLastReponseFile(){
		throw new Exception("Not implemented");
	}
	
	public function getARActes(){
		throw new Exception("Not implemented");
	}
	
	public function getDateAR($id_transaction){
		throw new Exception("Not implemented");
	}

	public function getBordereau($id_transaction){
		throw new Exception("Not implemented");
	}
	
	public function getStatusInfo($status_id){
		throw new Exception("Not implemented");
	}
	
	public function getFichierRetour($transaction_id){
		throw new Exception("Not implemented");
	}
	
}