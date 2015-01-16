<?php
class FakeIparapheur extends Connecteur {
	
	private $retour;
	private $iparapheur_type;
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		$this->retour = $collectiviteProperties->get('iparapheur_retour');
		$this->iparapheur_type = $collectiviteProperties->get('iparapheur_type');
	}
	
	public function getNbJourMaxInConnecteur(){
		return 30;
	}
	
	public function getSousType(){
		switch($this->iparapheur_type){
			case 'Actes':
				return array("Arrêté individuel","Arrêté réglementaire","Contrat et convention","Délibération");
			case 'PES':
				return array("BJ","Bordereau depense");
			case 'Document':
				return array("Courrier","Commande","Facture");
		}
		 
	}
	
	public function getDossierID($id,$name){
		$name = preg_replace("#[^a-zA-Z0-9_ ]#", "_", $name);
		return "$id $name";
	}
	
	public function sendDocument($typeTechnique,$sousType,$dossierID,$document_content,$content_type,array $all_annexes = array()){
		return "Dossier déposé pour signature";
	}
	
	public function getHistorique($dossierID){
		$date = date("d/m/Y H:i:s");
		if( $this->retour == 'Archive' ) {
			return $date . " : [Archive] Dossier signé (simulation de parapheur)!";
		}
		if( $this->retour == 'Rejet' ) {
			return $date . " : [RejetVisa] Dossier rejeté (simulation parapheur)!";
		}
		throw new Exception("Erreur provoquée par le simulateur du iParapheur");
	}
	
	public function getSignature($dossierID){
		$info['signature'] = "";
		$info['document'] = "Document";
		$info['nom_document'] = "document.txt";
		return $info;
	}
	
	public function sendHeliosDocument($typeTechnique,$sousType,$dossierID,$document_content,$content_type,$visuel_pdf){	
		return true;
	}
	
	public function getAllHistoriqueInfo($dossierID){
		if ($this->retour == 'Erreur'){
			throw new Exception("Erreur provoquée par le simulateur du iParapheur");
		}
		return array("Fake parapheur");
	}
	
	public function getLastHistorique($dossierID){
		
		if( $this->retour == 'Archive' ) {
			return "[Archive]";
		}
		return "[RejetVisa]";
		
	}
	
	public function effacerDossierRejete($dossierID){
		return true;
	}
}