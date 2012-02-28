<?php 


//http://stackoverflow.com/questions/5948402/having-issues-with-mime-headers-when-consuming-jax-ws-using-php-soap-client

class MySoapClient extends SoapClient {
	
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {	
    	$response = parent::__doRequest($request, $location, $action, $version, $one_way);
		$response = strstr($response,"<?xml");
        $response = strstr($response,"--uuid:",true);
		return $response;
    }
}


class IParapheur {
	
	private $lastError;
	private $wsdl;
	private $userCert;
	private $userCertPassword;
	private $login_http;
	private $password_http;
	
	public function __construct(DonneesFormulaire $collectiviteProperties){
		$this->wsdl = $collectiviteProperties->get("iparapheur_wsdl");
		$this->activate = $collectiviteProperties->get("iparapheur_activate");
		$this->userCert = $collectiviteProperties->getFilePath("iparapheur_user_key_pem");
		$this->userCertPassword = $collectiviteProperties->get("iparapheur_user_certificat_password");
		$this->login_http = $collectiviteProperties->get("iparapheur_login");
		$this->password_http = $collectiviteProperties->get("iparapheur_password");
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function getDossierID($id,$name){
		$name = preg_replace("#[^a-zA-Z0-9_ ]#", "_", $name);
		return "$id $name";
	}
	
	public function getSignature($dossierID){
		try{
			$result =  $this->getClient()->GetDossier($dossierID);
			if ($result->MessageRetour->codeRetour != 'OK'){
				$message = "[{$result->MessageRetour->severite}] {$result->MessageRetour->message}";
				$this->lastError = utf8_decode($message);
				return false;
			}
			
			
			if (isset($result->DocumentsAnnexes)){
				$info['document'] = $result->DocumentsAnnexes->DocAnnexe->fichier->_;
				$info['nom_document'] = $result->DocumentsAnnexes->DocAnnexe->nom;
			} else {
				$info['document'] = false;
				$info['nom_document'] = false;
			}
				
			if (isset($result->SignatureDocPrincipal)){
				$info['signature'] = $result->SignatureDocPrincipal->_;
			} else {
				$info['signature'] = false;
			}
			
			
			
			$this->archiver($dossierID);
			return $info;
		} catch (Exception $e){
		 	$this->lastError = "Erreur sur la récuperation de la signature : ".$e->getMessage();
			return false;			
		}
	}
	
	public function archiver($dossierID){
		try {
			$result = $this->getClient()->ArchiverDossier(array("DossierID" => $dossierID,"ArchivageAction"=>"EFFACER"));
		} catch(Exception $e){
			$this->lastError = $e->getMessage();
			return false;
		}
		return $result;
	}
	
	public function effacerDossierRejete($dossierID){
		try {
			$result = $this->getClient()->EffacerDossierRejete($dossierID);
		} catch(Exception $e){
			$this->lastError = $e->getMessage();
			return false;
		}
		return $result;
	}
	
	
	public function getHistorique($dossierID){
		try{
			$result =  $this->getClient()->GetHistoDossier($dossierID);
			
			if ( empty($result->LogDossier)){
				$this->lastError = "Le dossier n'a pas été trouvé";
				return false;
			}
			
			$lastLog = end($result->LogDossier);
			$date = date("d/m/Y H:i:s",strtotime($lastLog->timestamp));
			return utf8_decode($date . " : [" . $lastLog->status . "] ".$lastLog->annotation);
		}  catch (Exception $e){
			$this->lastError = $e->getMessage();
			return false;			
		}
	}
	
	public function sendDocument($typeTechnique,$sousType,$dossierID,$document_content,$content_type,array $all_annexes = array()){
		$client = $this->getClient();		
		try {
			
			$data = array(
						"TypeTechnique"=>utf8_encode($typeTechnique),
						"SousType"=> utf8_encode($sousType),
						"DossierID" => $dossierID,
						"DocumentPrincipal" => array("_"=>$document_content,"contentType"=>$content_type),
						"Visibilite" => "SERVICE",
						
				); 
			if ($all_annexes){
				$data["DocumentsAnnexes"] = array();
			}
			foreach($all_annexes as $annexe){
					$data["DocumentsAnnexes"][] = array("nom"=>$annexe['name'],
													"fichier" => array("_"=>$annexe['file_content'],
													"contentType"=>$annexe['content_type']),
													"mimetype" => $annexe['content_type'],
													"encoding" => "UTF-8"
				);
				
			}
			$result =  $client->CreerDossier($data);
				
			
			/*"DocumentsAnnexes" => array(
				nom=>"",
				fichier=>"",
				mimetype => "",
			
			)*/
			
			$messageRetour = $result->MessageRetour;
			$message = "[{$messageRetour->severite}] {$messageRetour->message}";
			if ($messageRetour->codeRetour == "KO"){
				$this->lastError = utf8_decode($message);
				return false;
			} elseif($messageRetour->codeRetour == "OK") {
				return utf8_decode($message);
			} else {
				$this->lastError = "Le iparapheur n'a pas retourné de code de retour : " . utf8_decode($message);
				return false;
			}		
		} catch (Exception $e){
			$this->lastError = $e->getMessage() . $client->__getLastResponse();
			return false;			
		}
		
	}
	
	public function sendDocumentTest(){
		$dossierID = mt_rand();
		$document = file_get_contents(PASTELL_PATH . "/data-exemple/exemple.pdf");		
		return $this->sendDocument("Actes","Deliberation",$dossierID,$document,"application/pdf");
	}
	
	
	private function getClient(){
		static $client;
		if ($client) {
			return $client;
		}
		if ( ! $this->activate){
			$this->lastError = "Le module n'est pas activé";
			throw new Exception("Le module n'est pas activé");
		}
		if (! $this->wsdl ){
			$this->lastError = "Le WSDL n'a pas été fourni";
			throw new Exception("Le WSDL n'a pas été fourni");
		}
		try {
			$client = new MySoapClient(
				$this->wsdl,
				array(
	     			'local_cert' => $this->userCert,
	     			'passphrase' => $this->userCertPassword,
					'login' => $this->login_http,
					'password' => $this->password_http,
					'trace' => 1
	    		));
		} catch (Exception $e){
			$this->lastError = "Connexion - " . $e->getMessage();
			throw new Exception($this->lastError );
		}
		return $client;
	} 
	
	public function getType(){
		try{
			return $this->getClient()->GetListeTypes();
		}  catch (Exception $e){
			$this->lastError = $e->getMessage();
			return false;			
		}
	}
	
	public function getSousType($type){
		try{
			$sousType = $this->getClient()->GetListeSousTypes($type)->SousType;
			foreach($sousType as $n => $v){
				$sousType[$n] = utf8_decode($v);
			}
			return $sousType;
		}  catch (Exception $e){
			$this->lastError = $e->getMessage();
			return false;			
		}
	}
	
	public function testConnexion(){
		try{
			return $this->getClient()->echo("test_connexion_pastell");
		}  catch (Exception $e){
			$this->lastError = $e->getMessage() . $this->getClient()->__getLastResponse();
			return false;
		}
	}
	
}
