<?php
class Libersign extends SignatureConnecteur {
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
	}
	
	public function getNbJourMaxInConnecteur(){
		throw new Exception("Not implemented");
	}
	
	public function getSousType(){
		throw new Exception("Not implemented");
	}
	
	public function getDossierID($id,$name){
		return "n/a";
	}
	
	public function sendDocument($typeTechnique,$sousType,$dossierID,$document_content,$content_type,array $all_annexes = array()){
		throw new Exception("Not implemented --");
	}
	
	public function getHistorique($dossierID){
		throw new Exception("Not implemented");
	}
	
	public function getSignature($dossierID){
		throw new Exception("Not implemented");
	}
	
	public function sendHeliosDocument($typeTechnique,$sousType,$dossierID,$document_content,$content_type,$visuel_pdf){	
		throw new Exception("Not implemented");
	}
	
	public function getAllHistoriqueInfo($dossierID){
		throw new Exception("Not implemented");
	}
	
	public function getLastHistorique($dossierID){
		throw new Exception("Not implemented");		
	}
	
	public function effacerDossierRejete($dossierID){
		throw new Exception("Not implemented");
	}
	
	public function isLocalSignature(){
		return true;
	}

}