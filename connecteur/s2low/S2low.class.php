<?php


class S2low  extends TdtConnecteur {
	
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
	const URL_ACTES_REPONSE_PREFECTURE =  "/modules/actes/actes_transac_get_document.php";
	const URL_POST_REPONSE_PREFECTURE = "/modules/actes/actes_transac_reponse_create.php";
	const URL_ACTES_TAMPONNE = "/modules/actes/actes_transac_get_tampon.php";
	const URL_POST_CONFIRM = "/modules/actes/actes_transac_post_confirm_api.php";
	
	const URL_GET_FILE_LIST = "/modules/actes/actes_transac_get_files_list.php";
	const URL_DOWNLOAD_FILE = "/modules/actes/actes_download_file.php";
	
	
	private $arActes;
	private $reponseFile;
	
	protected $curlWrapper;
	
	protected $ensureLogin;
	protected $en_attente;
	protected $authentication_for_teletransmisson;
	
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		$this->curlWrapper = new CurlWrapper();
		$this->curlWrapper->setServerCertificate($collectiviteProperties->getFilePath('server_certificate'));	
		$this->curlWrapper->dontVerifySSLCACert();
		$this->curlWrapper->setClientCertificate(	$collectiviteProperties->getFilePath('user_certificat_pem'),
													$collectiviteProperties->getFilePath('user_key_pem'),
													$collectiviteProperties->get('user_certificat_password'));

		if ($collectiviteProperties->get("user_login")){
			$this->curlWrapper->httpAuthentication($collectiviteProperties->get("user_login"), $collectiviteProperties->get("user_password"));
			$this->ensureLogin = true;
		}						
		$this->isActivate = $collectiviteProperties->get('activate');
		$this->tedetisURL = $collectiviteProperties->get('url');
		$this->classificationFile = $collectiviteProperties->getFilePath('classification_file');	
		$this->en_attente = $collectiviteProperties->get('envoi_en_attente');
		$this->authentication_for_teletransmisson = $collectiviteProperties->get('authentication_for_teletransmisson');
	}
	

	protected function ensureLogin(){	
		if ($this->ensureLogin){
			return true;
		}
		
		$output = $this->curlWrapper->get($this->tedetisURL .self::URL_LIST_LOGIN);
		
		
		if ($this->curlWrapper->getLastError()){
			throw new S2lowException($this->curlWrapper->getLastError());
		}

		if ($output){
			$this->ensureLogin = true;
			return true;
		}
		throw new S2lowException("La connexion S²low nécessite un login/mot de passe ");		
	}
	
	private function exec($url){
		$this->ensureLogin();
		$output = $this->curlWrapper->get($this->tedetisURL .$url);
		
		$error = $this->curlWrapper->getLastError();
		if ( ! $output && $error){
			throw new S2lowException($error);			
		}		
		return $output;
	}
	
	
	public function getLogicielName(){
		return "S²low";
	}
	
	public function testConnexion(){
		$this->exec(self::URL_TEST);
	}
	
	public function getClassification(){
		$result = $this->exec( self::URL_CLASSIFICATION ."?api=1");
		if (!$result){
			throw new S2lowException($this->curlWrapper->getLastError());
		}
		if (preg_match("/^KO/",$result)){
			throw new S2lowException("S²low a répondu : " .$result);
		}
		return $result;
	}
	
	public function demandeClassification(){
		$result = $this->exec( self::URL_DEMANDE_CLASSIFICATION ."?api=1");
		if (preg_match("/^KO/",$result)){
			throw new S2lowException("S²low a répondu : " .$result);
		}
		return "S²low a répondu : " .$result;
	}
	
	public function annulationActes($id_transaction){
		$this->curlWrapper->addPostData('api',1);
		$this->curlWrapper->addPostData('id',$id_transaction);
		$result = $this->exec( self::URL_ANNULATION );	
		if( ! $result ){
			throw new S2lowException("Erreur lors de la connexion a S²low (".$this->tedetisURL.")");
		}	
				
		if (! preg_match("/^OK/",$result)){
			throw new S2lowException("Erreur lors de la transmission, S²low a répondu : $result");
		}
		$ligne = explode("\n",$result);
		$id_transaction = trim($ligne[1]);
		return $id_transaction;
	}
	
	public function verifClassif(){
		
		if (! is_file($this->classificationFile)){
			throw new S2lowException("Il n'y a pas de fichier de classification Actes");
		}
		
		$usingClassif = file_get_contents($this->classificationFile);
		$theClassif = $this->getClassification();
	
		if ($usingClassif != $theClassif){
			throw new S2lowException("La classification utilisée n'est plus à jour");
		}
		return true;
	}
	
	public function postHelios(DonneesFormulaire $donneesFormulaire){
		$file_path = $donneesFormulaire->getFilePath('fichier_pes_signe');
		$file_name = $donneesFormulaire->get('fichier_pes_signe');
		//$ifile_name = $file_name[0];
		$file_name = preg_replace("#[^a-zA-Z0-9._ ]#", "_", $file_name[0]);
		$this->curlWrapper->addPostFile('enveloppe',$file_path,$file_name);
		$result = $this->exec( self::URL_POST_HELIOS );	
		$xml = simplexml_load_string($result);
		if (! $xml){
			throw new S2lowException("La réponse de S²low n'a pas pu être analysé : (".$result.")");
		}
		
		if ($xml->resultat == "OK"){
			$donneesFormulaire->setData('tedetis_transaction_id',$xml->id);
			return true;
		}
		throw new S2lowException( "Erreur lors de l'envoi du PES : " . utf8_decode($xml->message));
	}
	
	
	private function getIsEnAttente(){
		if ( $this->en_attente){
			return 1;
		}
		if ($this->authentication_for_teletransmisson){
			return 1;
		}
		return 0;
	}
	
	public function postActes(DonneesFormulaire $donneesFormulaire) {
		$this->verifClassif();
		
		$this->curlWrapper->addPostData('api',1);
		$this->curlWrapper->addPostData('nature_code',$donneesFormulaire->get('acte_nature'));
		
		$this->curlWrapper->addPostData('number',$donneesFormulaire->get('numero_de_lacte'));
		$this->curlWrapper->addPostData('subject',$donneesFormulaire->get('objet'));
		
		$this->curlWrapper->addPostData('decision_date', date("Y-m-d", strtotime($donneesFormulaire->get('date_de_lacte'))));
		$this->curlWrapper->addPostData('en_attente', $this->getIsEnAttente());
		
		$file_path = $donneesFormulaire->getFilePath('arrete');
		$file_name = $donneesFormulaire->get('arrete');
		//$file_name = $file_name[0];
		$file_name = preg_replace("#[^a-zA-Z0-9._ ]#", "_", $file_name[0]);
		
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
			throw new S2lowException("Erreur lors de la connexion à S²low (".$this->tedetisURL.")");
		}	
				
		if (! preg_match("/^OK/",$result)){
			throw new S2lowException("Erreur lors de la transmission, S²low a répondu : $result");
		}
		
		$ligne = explode("\n",$result);
		$id_transaction = trim($ligne[1]);
		$donneesFormulaire->setData('tedetis_transaction_id',$id_transaction);
		return true;		
	}
	
	public function getStatusHelios($id_transaction){
		$result = $this->exec(self::URL_STATUS_HELIOS."?transaction=$id_transaction");		
		$xml = simplexml_load_string($result);
		if (! $xml){
			throw new S2lowException("La réponse de S²low n'a pas pu être analysé : (".$result.")");
		}
		
		if ($xml->resultat == "KO"){
			throw new S2lowException($xml->message);
			return false;
		}
		$this->reponseFile = $result;
		return strval($xml->status);
	}
	
	
	public function getStatus($id_transaction){
		$result = $this->exec(self::URL_STATUS."?transaction=$id_transaction");
		
		$ligne = explode("\n",$result);
		
		if (trim($ligne[0]) != 'OK'){
			throw new S2lowException(trim($ligne[1]));
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
		return (substr($result, strpos($result, 'actes:DateReception')+21, 10));
	}

	public function getBordereau($id_transaction){
		$result = $this->exec(self::URL_BORDEREAU."?trans_id=$id_transaction");
		return $result;
	}
	
	/**
	 * Fonction compatible S2low v2 et S2low < v2
	 * @see TdtConnecteur::getActeTamponne()
	 */
	public function getActeTamponne($id_transaction){
		$file_list = $this->getActeTamponneS2lowV2FileList($id_transaction);
		if (! $file_list){
			//S2low v<2
			$result = $this->exec(self::URL_ACTES_TAMPONNE."?transaction=$id_transaction");
			return $result;
		}
		//S2low v2
		return $this->getActeTamponneS2lowV2($file_list);
	}
	
	private function getActeTamponneS2lowV2FileList($id_transaction){
		try{
			$file_list = $this->exec(self::URL_GET_FILE_LIST."?transaction=$id_transaction");
		} catch(Exception $e){
			return false;
		}
		if (!$file_list){
			return false;
		}
		$file_list = json_decode($file_list,true);
		if (!$file_list){
			return false;
		}
		return $file_list;
		
	} 
	
	private function getActeTamponneS2lowV2($file_list){
		if($file_list[1]['mimetype'] != 'application/pdf'){
			return false;
		}
		
		$result = $this->exec(self::URL_DOWNLOAD_FILE."?file={$file_list[1]['id']}&tampon=true");
		return $result;
	}
	
	
	public function getStatusInfo($status_id){
		//Note : les status helios et actes sont commun sur le TdT pour la plupart.
		$all_status = array (
					-1 => "Erreur",0 =>"Annulé","Posté","En attente de transmission. Fichier valide.","Transmis","Acquittement reçu","status 5 invalide","Refusé","En traitement","Information disponible");
		if (empty($all_status[$status_id])){
			return "Status $status_id inconnu sur Pastell";
		}
		return $all_status[$status_id];
	}
	
	public function getFichierRetour($transaction_id){
		$result = $this->exec(self::URL_HELIOS_RETOUR."?id=$transaction_id");
		return $result;
	}
	
	public function getListReponsePrefecture($transaction_id){
		$result = array();
		$all_reponse = $this->exec(self::URL_ACTES_REPONSE_PREFECTURE."?id=$transaction_id");
		$all_reponse = trim($all_reponse);
		if (!$all_reponse){
			return $result;
		}
		foreach(explode("\n",$all_reponse) as $line){
			list($type,$status,$id) = explode("-",$line);
			$result[] = array('type'=>$type,'status'=>$status,'id'=>$id);
		}
		return $result;
	}
	
	public function getReponsePrefecture($transaction_id){
		return $this->exec(self::URL_ACTES_REPONSE_PREFECTURE."?id=$transaction_id");
	}
	
	public function sendResponse(DonneesFormulaire $donneesFormulaire) {
		foreach(array(2,3,4) as $id_type) {
			$libelle = $this->getLibelleType($id_type);
			if($donneesFormulaire->get("has_$libelle") == true){
				if ($donneesFormulaire->get("has_reponse_$libelle") == false){
					$this->sendReponseType($id_type,$donneesFormulaire);	
				}
			}
		}
	}
	
	
	private function getLibelleType($id_type){
		$txt_message = array(TdTConnecteur::COURRIER_SIMPLE => 'courrier_simple',
							'demande_piece_complementaire',
							'lettre_observation',
							'defere_tribunal_administratif');
		return $txt_message[$id_type];
	}
	
	
	private function sendReponseType($id_type,$donneesFormulaire){
		
		$libelle = $this->getLibelleType($id_type);

		$nature_reponse = $donneesFormulaire->get("nature_reponse_$libelle");
		$file_name = $donneesFormulaire->getFileName("reponse_" . $libelle);
		$file_path = $donneesFormulaire->getFilePath("reponse_" . $libelle);
		$id = $donneesFormulaire->get("{$libelle}_id");

		$this->curlWrapper->addPostData('id',$id);
		$this->curlWrapper->addPostData('api',1);
		$this->curlWrapper->addPostData('type_envoie',$nature_reponse);
		$this->curlWrapper->addPostFile('acte_pdf_file',$file_path,$file_name);
		 
		if (($id_type == 3) && $donneesFormulaire->get('reponse_pj_demande_piece_complementaire')){
			foreach($donneesFormulaire->get('reponse_pj_demande_piece_complementaire') as $i => $file_name){
				$file_path = $donneesFormulaire->getFilePath('reponse_pj_demande_piece_complementaire',$i);
				$this->curlWrapper->addPostFile('acte_attachments[]', $file_path,$file_name) ;
			}
		}
			
		$result = $this->exec( self::URL_POST_REPONSE_PREFECTURE );	
		if (! preg_match("/^OK/",$result)){
			throw new S2lowException("Erreur lors de la transmission, S²low a répondu : $result");
		}
		
		$ligne = explode("\n",$result);
		$id_transaction = trim($ligne[1]);
		$donneesFormulaire->setData("{$libelle}_response_transaction_id",$id_transaction);
		$donneesFormulaire->setData("has_reponse_{$libelle}",true);
		return true;
	}
	
	public function getRedirectURLForTeletransimission(){
		return $this->tedetisURL .self::URL_POST_CONFIRM;
	}
	
	//Cette fonction fonctionne sur une branche de S2low 1.5 ou 2.0
	//Elle ne lance pas d'exception (la branche 1.5 ne connait pas cette fonction).
	//Lorsque la version 1.5 de S2low n'existera plus, il conviendra de modifier la fonction
	//pour qu'elle déclenche de véritables erreurs en cas de problème.
	public function getAnnexesTamponnees($transaction_id){
		try{
			$file_list = $this->exec(self::URL_GET_FILE_LIST."?transaction=$transaction_id");
		} catch(Exception $e){
			return array();
		}
		if (!$file_list){
			return array();
		}
		$file_list = json_decode($file_list,true);
		if (!$file_list){
			return array();
		}
		
		if (count($file_list)<=2){
			return array();
		}
		array_shift($file_list);
		array_shift($file_list);
		$result = array();
		foreach($file_list as $file){
			if($file['mimetype'] != 'application/pdf'){
				continue;
			}
			$result[] = $this->exec(self::URL_DOWNLOAD_FILE."?file={$file['id']}&tampon=true");	
		}
		
		return $result;
	} 
	
}

class S2lowException extends TdTException {}

