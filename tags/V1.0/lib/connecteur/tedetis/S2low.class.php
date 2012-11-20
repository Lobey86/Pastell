<?php



class S2low extends Tedetis {
	
	const URL_TEST = "/modules/actes/";
	const URL_CLASSIFICATION = "/modules/actes/actes_classification_fetch.php";
	const URL_POST_ACTES =  "/modules/actes/actes_transac_create.php";
	const URL_STATUS = "/modules/actes/actes_transac_get_status.php";
	const URL_ANNULATION = "/modules/actes/actes_transac_cancel.php";
	const URL_BORDEREAU = "/modules/actes/actes_create_pdf.php";
	const URL_DEMANDE_CLASSIFICATION = "/modules/actes/actes_classification_request.php";
	const URL_POST_HELIOS = "/modules/helios/api/helios_importer_fichier.php";
	const URL_STATUS_HELIOS =  "/modules/helios/api/helios_transac_get_status.php";
	const URL_HELIOS_RETOUR = "/modules/helios/helios_download_acquit.php";
	const URL_LIST_LOGIN = "/admin/users/api-list-login.php";

	private $arActes;
	private $reponseFile;
	
	protected $curlWrapper;
	
	protected $ensureLogin;
	
	
	public function __construct(DonneesFormulaire $collectiviteProperties){
		parent::__construct($collectiviteProperties);
		$this->curlWrapper = new CurlWrapper();
		$this->curlWrapper->setServerCertificate($collectiviteProperties->getFilePath('tdt_server_certificate'));	
		$this->curlWrapper->setClientCertificate(	$collectiviteProperties->getFilePath('tdt_user_certificat_pem'),
													$collectiviteProperties->getFilePath('tdt_user_key_pem'),
													$collectiviteProperties->get('tdt_user_certificat_password'));

		if ($collectiviteProperties->get("tdt_user_login")){
			$this->curlWrapper->httpAuthentication($collectiviteProperties->get("tdt_user_login"), $collectiviteProperties->get("tdt_user_password"));
			$this->ensureLogin = true;
		}												
	}
	
	

	private function ensureLogin(){
		if ($this->ensureLogin){
			return true;
		}
		$output = $this->curlWrapper->get($this->tedetisURL .self::URL_LIST_LOGIN);
		if (!$output){
			$this->ensureLogin = true;
			return true;
		}
		$this->lastError = "La connexion S²low nécessite un login/mot de passe ";
		return false;
		
	}
	
	private function exec($url){
		if (! $this->isActivate){
			$this->lastError = "Ce module n'est pas activé";
			return false;
		}
		if (! $this->ensureLogin()){
			return false;
		}
		$output = $this->curlWrapper->get($this->tedetisURL .$url);
		if ( ! $output){
			$this->lastError = $this->curlWrapper->getLastError();
			return false;
		}		
		return $output;
	}
	
	
	public function getLogicielName(){
		return "S²low";
	}
	
	public function testConnexion(){
		return $this->exec(self::URL_TEST)?true:false;
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
		if (! $result){
			return false;
		}
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
	
	public function postHelios(DonneesFormulaire $donneesFormulaire){
		$file_path = $donneesFormulaire->getFilePath('fichier_pes_signe');
		$file_name = $donneesFormulaire->get('fichier_pes_signe');
		$file_name = $file_name[0];
		$this->curlWrapper->addPostFile('enveloppe',$file_path,$file_name);
		$result = $this->exec( self::URL_POST_HELIOS );	
		if( ! $result ){
			$this->lastError = "Erreur lors de la connexion à S²low (".$this->tedetisURL.")";
			return false;
		}	
		$xml = simplexml_load_string($result);
		if (! $xml){
			$this->lastError = "La réponse de S²low n'a pas pu être analysé : (".$result.")";
			return false;
		}
		
		if ($xml->resultat == "OK"){
			$donneesFormulaire->setData('tedetis_transaction_id',$xml->id);
			return true;
		}
		$this->lastError = "Erreur lors de l'envoi du PES : " . utf8_decode($xml->message);
		return false;
	}
	
	
	public function postActes(DonneesFormulaire $donneesFormulaire) {
		
		if ( ! $this->verifClassif()){
			return false;
		}
		
		$this->curlWrapper->addPostData('api',1);
		$this->curlWrapper->addPostData('nature_code',$donneesFormulaire->get('acte_nature'));
		
		$this->curlWrapper->addPostData('number',$donneesFormulaire->get('numero_de_lacte'));
		$this->curlWrapper->addPostData('subject',$donneesFormulaire->get('objet'));
		
		$this->curlWrapper->addPostData('decision_date', date("Y-m-d", strtotime($donneesFormulaire->get('date_de_lacte'))));
		
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
	
	public function getStatusHelios($id_transaction){
		$result = $this->exec(self::URL_STATUS_HELIOS."?transaction=$id_transaction");
		if( ! $result ){
			$this->lastError = "Erreur lors de la connexion à S²low (".$this->tedetisURL.")";
			return false;
		}
		$xml = simplexml_load_string($result);
		if (! $xml){
			$this->lastError = "La réponse de S²low n'a pas pu être analysé : (".$result.")";
			return false;
		}
		
		if ($xml->resultat == "KO"){
			$this->lastError = $xml->message;
			return false;
		}
		$this->reponseFile = $result;
		
		return strval($xml->status);
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
		
		$result = trim($ligne[1]);
		if ($result == 4){
 			array_shift($ligne);
 			array_shift($ligne);
			$this->arActes = implode("\n",$ligne); 
		}
		
		return $result;
	}
	
	public function getLastReponseFile(){
		return $this->reponseFile;
	}
	
	public function getARActes(){
		return $this->arActes;
	}

	public function getDateAR($id_transaction){
		$result = $this->exec(self::URL_STATUS."?transaction=$id_transaction");
		if (! $result){
        	return false;
		}
		return (substr($result, strpos($result, 'actes:DateReception')+21, 10));
	}

	public function getBordereau($id_transaction){
		$result = $this->exec(self::URL_BORDEREAU."?trans_id=$id_transaction");
		return $result;
	}
	
	public function getStatusInfo($status_id){
		$all_status = array (
					-1 => "Erreur",0 =>"Annulé","Posté","status 2 invalide","Transmis","Acquittement reçu","status 5 invalide","Refusé","En traitement","Information disponible");
		if (empty($all_status[$status_id])){
			return "Status $status_id inconnu sur Pastell";
		}
		return $all_status[$status_id];
	}
	
	public function getFichierRetour($transaction_id){
		$result = $this->exec(self::URL_HELIOS_RETOUR."?id=$transaction_id");
		return $result;
	}
	
}
