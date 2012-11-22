<?php
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");

abstract class Tedetis {
	
	const STATUS_ERREUR = 1;
	const STATUS_ANNULE = 0;
	const STATUS_POSTE = 1;
	const STATUS_EN_ATTENTE_DE_TRANSMISSION = 2;
	const STATUS_TRANSMIS = 3;
	const STATUS_ACQUITTEMENT_RECU = 4;
	const STATUS_VALIDE = 5;
	const STATUS_REFUSE = 6;
	
	const STATUS_HELIOS_TRAITEMENT = 7;
	const STATUS_HELIOS_INFO = 8;
		
	public static function getStatusString($status){
		$statusString = array(-1=>'Erreur','Annulé','Posté','En attente de transmission','Transmis','Acquittement reçu','Validé','Refusé');
		if (empty($statusString[$status])){
			return "Statut inconnu ($status)";
		}
		return $statusString[$status] ;
	}
	
	protected $isActivate;
	protected $tedetisURL;
	protected $classificationFile;
	
	protected $lastError;

	public function __construct(DonneesFormulaire $collectiviteProperties){
		$this->isActivate = $collectiviteProperties->get('tdt_activate');
		$this->tedetisURL = $collectiviteProperties->get('tdt_url');
		$this->classificationFile = $collectiviteProperties->getFilePath('classification_file');
	}

	public function getLastError(){
		return $this->lastError;
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
	
	abstract public function getStatusInfo($status_id);
	
	abstract public function getFichierRetour($transaction_id);
	
}
