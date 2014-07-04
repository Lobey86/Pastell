<?php

class TdTException extends Exception {}

abstract class TdtConnecteur extends Connecteur{
	
	const STATUS_ERREUR = -1;
	const STATUS_ANNULE = 0;
	const STATUS_POSTE = 1;
	const STATUS_EN_ATTENTE_DE_TRANSMISSION = 2;
	const STATUS_TRANSMIS = 3;
	const STATUS_ACQUITTEMENT_RECU = 4;
	const STATUS_VALIDE = 5;
	const STATUS_REFUSE = 6;
	
	const STATUS_HELIOS_TRAITEMENT = 7;
	const STATUS_HELIOS_INFO = 8;
	const STATUS_HELIOS_ATTENTE = 9;
	
	const STATUS_ACTES_MESSAGE_PREF_RECU = 7 ;
	const STATUS_ACTES_MESSAGE_PREF_ENVOYE = 8 ;
	
	const STATUS_ACTES_MESSAGE_PREF_ACQUITTEMENT_RECU = 11;
	
	const STATUS_ACTES_EN_ATTENTE_DE_POSTER = 17;
	
	
	const COURRIER_SIMPLE = 2;
	const DEMANDE_PIECE_COMPLEMENTAIRE = 3;
	const LETTRE_OBSERVATION = 4;
	const DEFERE_TRIBUNAL_ADMINISTRATIF = 5;
	 	
	public static function getStatusString($status){
		$statusString = array(-1=>'Erreur','Annulé','Posté','En attente de transmission','Transmis','Acquittement reçu','Validé','Refusé','AR non disponible pour le moment',
			17=>"En attente d'être postée");
		if (empty($statusString[$status])){
			return "Statut inconnu ($status)";
		}
		return $statusString[$status] ;
	}
	
	abstract public function getLogicielName();
	
	abstract public function testConnexion();
	
	abstract public function getClassification();
	
	abstract public function demandeClassification();
	
	abstract public function annulationActes($id_transaction);
	
	abstract public function verifClassif();
	
	abstract public function postHelios(DonneesFormulaire $donneesFormulaire);
	
	abstract public function postActes(DonneesFormulaire $donneesFormulaire);
	
	abstract public function getStatusHelios($id_transaction);
	
	abstract public function getStatus($id_transaction);
	
	abstract public function getLastReponseFile();
	
	abstract public function getARActes();
	
	abstract public function getDateAR($id_transaction);

	abstract public function getBordereau($id_transaction);
	
	abstract public function getActeTamponne($id_transaction);
	
	abstract public function getStatusInfo($status_id);
	
	abstract public function getFichierRetour($transaction_id);
	
	abstract public function getListReponsePrefecture($transaction_id);
	
	abstract public function getReponsePrefecture($transaction_id);
	
	abstract public function sendResponse(DonneesFormulaire $donneesFormulaire);
	
}
