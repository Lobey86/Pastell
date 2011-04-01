<?php

require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/base/CurlWrapper.class.php");

class Tedetis {
	
	const STATUS_ERREUR = 1;
	const STATUS_ANNULE = 0;
	const STATUS_POSTE = 1;
	const STATUS_EN_ATTENTE_DE_TRANSMISSION = 2;
	const STATUS_TRANSMIS = 3;
	const STATUS_ACQUITTEMENT_RECU = 4;
	const STATUS_VALIDE = 5;
	const STATUS_REFUSE = 6;
	
	
	const URL_TEST = "/modules/actes/";
	const URL_CLASSIFICATION = "/modules/actes/actes_classification_fetch.php";
	const URL_POST_ACTES =  "/modules/actes/actes_transac_create.php";
	const URL_STATUS = "/modules/actes/actes_transac_get_status.php";
	const URL_ANNULATION = "/modules/actes/actes_transac_cancel.php";
	const URL_BORDEREAU = "/modules/actes/actes_create_pdf.php";
	const URL_DEMANDE_CLASSIFICATION = "/modules/actes/actes_classification_request.php";
	
	public static function getStatusString($status){
		$statusString = array(-1=>'Erreur','Annulé','Posté','En attente de transmission','Transmis','Acquittement reçu','Validé','Refusé');
		if (empty($statusString[$status])){
			return "Statut inconnu ($status)";
		}
		return $statusString[$status] ;
	}
	
	
	private $isActivate;
	private $tedetisURL;
	
	private $curlWrapper;
	private $lastError;
	
	private $classificationFile;
	
	public function __construct(DonneesFormulaire $collectiviteProperties){
		
		$this->setCurlWrapper(new curlWrapper());
		
		$this->isActivate = $collectiviteProperties->get('tdt_activate');
		$this->tedetisURL = $collectiviteProperties->get('tdt_url');
		
		$this->curlWrapper->setServerCertificate($collectiviteProperties->getFilePath('tdt_server_certificate'));
		
		$this->curlWrapper->setClientCertificate(	$collectiviteProperties->getFilePath('tdt_user_certificat_pem'),
													$collectiviteProperties->getFilePath('tdt_user_key_pem'),
													$collectiviteProperties->get('tdt_user_certificat_password'));
		$this->classificationFile = $collectiviteProperties->getFilePath('classification_file');
	}

	public function setCurlWrapper(CurlWrapper $curlWrapper){
		$this->curlWrapper = $curlWrapper;
	}

	public function getLastError(){
		return $this->lastError;
	}
	
	private function exec($url){
		if (! $this->isActivate){
			$this->lastError = "Ce module n'est pas activé";
			return false;
		}
		
		$output = $this->curlWrapper->get($this->tedetisURL .$url);
		
		if ( ! $output){
			$this->lastError = $this->curlWrapper->getLastError();
			return false;
		}		
		return $output;
	}
	
	public function testConnexion(){
		return $this->exec(self::URL_TEST);
	}
	
	public function getClassification(){
		
		$result = $this->exec( self::URL_CLASSIFICATION ."?api=1");
		if (preg_match("/^KO/",$result)){
			$this->lastError = "S²low a répondu : " .$result;
			return false;
		}
		return $result;
	}
	
	public function demandeClassification(){
		$result = $this->exec( self::URL_DEMANDE_CLASSIFICATION ."?api=1");
		if (preg_match("/^KO/",$result)){
			$this->lastError = "S²low a répondu : " .$result;
			return false;
		}
		return "S²low a répondu : " .$result;
	}
	
	public function annulationActes($id_transaction){
		$this->curlWrapper->addPostData('api',1);
		$this->curlWrapper->addPostData('id',$id_transaction);
		$result = $this->exec( self::URL_ANNULATION );	
		if( ! $result ){
			$this->lastError = "Erreur lors de la connexion a S²low (".$this->tedetisURL.")";
			return false;
		}	
				
		if (! preg_match("/^OK/",$result)){
			$this->lastError = "Erreur lors de la transmission, S²low a répondu : $result";
			return false;
		}
		return true;
	}
	
	public function verifClassif(){
		
		if (! is_file($this->classificationFile)){
			$this->lastError = "Il n'y a pas de fichier de classification Actes";
			return false;
		}
		
		$usingClassif = file_get_contents($this->classificationFile);
		$theClassif = $this->getClassification();
	
		if ($usingClassif != $theClassif){
			$this->lastError = "La classification utilisée n'est plus à jour";
			return false;
		}
		return true;
	}
	
	
	public function postActes(DonneesFormulaire $donneesFormulaire) {
		
		if ( ! $this->verifClassif()){
			return false;
		}
		
		$this->curlWrapper->addPostData('api',1);
		$this->curlWrapper->addPostData('nature_code',$donneesFormulaire->get('acte_nature'));
		
		$this->curlWrapper->addPostData('number',$donneesFormulaire->get('numero_de_lacte'));
		$this->curlWrapper->addPostData('subject',$donneesFormulaire->get('objet'));
		$this->curlWrapper->addPostData('decision_date',$donneesFormulaire->get('date_de_lacte'));
		
		$file_path = $donneesFormulaire->getFilePath('arrete');
		$file_name = $donneesFormulaire->get('arrete');
		$file_name = $file_name[0];
		$this->curlWrapper->addPostFile('acte_pdf_file',$file_path,$file_name);
				
		if ($donneesFormulaire->get('autre_document_attache')){
			foreach($donneesFormulaire->get('autre_document_attache') as $i => $file_name){
				$file_path = $donneesFormulaire->getFilePath('autre_document_attache',$i);
				$this->curlWrapper->addPostFile('acte_attachments[]', $file_path,$file_name) ;
			}
		}
		
		$classification  = $donneesFormulaire->get('classification');
		$c1 = explode(" ",$classification);
		$dataClassif = explode(".",$c1[0]);
		
		foreach($dataClassif as $i => $elementClassif){
			$this->curlWrapper->addPostData('classif' . ( $i + 1), $elementClassif);  
		}
		
		$result = $this->exec( self::URL_POST_ACTES );	
		if( ! $result ){
			$this->lastError = "Erreur lors de la connexion à S²low (".$this->tedetisURL.")";
			return false;
		}	
				
		if (! preg_match("/^OK/",$result)){
			$this->lastError = "Erreur lors de la transmission, S²low a répondu : $result";
			return false;
		}
		
		$ligne = explode("\n",$result);
		$id_transaction = trim($ligne[1]);
		$donneesFormulaire->setData('tedetis_transaction_id',$id_transaction);
		
		return true;		
	}
	
	public function getStatus($id_transaction){
		$result = $this->exec(self::URL_STATUS."?transaction=$id_transaction");
		if (! $result){
			return false;
		}
		$ligne = explode("\n",$result);
		if (trim($ligne[0]) != 'OK'){
			$this->lastError = trim($ligne[1]);
			return false;
		}
		
		return trim($ligne[1]);
	}
	
	public function getBordereau($id_transaction){
		$result = $this->exec(self::URL_BORDEREAU."?trans_id=$id_transaction");
		return $result;
	}
}