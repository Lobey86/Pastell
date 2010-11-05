<?php

require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/base/CurlWrapper.class.php");

class Tedetis {
	
	const STATUS_ACQUITTEMENT_RECU = 4;
	
	const URL_TEST = "/modules/actes/";
	const URL_CLASSIFICATION = "/modules/actes/actes_classification_fetch.php";
	const URL_POST_ACTES =  "/modules/actes/actes_transac_create.php";
	const URL_STATUS = "/modules/actes/actes_transac_get_status.php";
	
	private $isActivate;
	private $tedetisURL;
	
	private $curlWrapper;
	private $lastError;
	
	public function __construct(DonneesFormulaire $collectiviteProperties){
		
		$this->setCurlWrapper(new curlWrapper());
		
		$this->isActivate = $collectiviteProperties->get('tdt_activate');
		$this->tedetisURL = $collectiviteProperties->get('tdt_url');
		
		$this->curlWrapper->setServerCertificate($collectiviteProperties->getFilePath('tdt_server_certificate'));
		
		$this->curlWrapper->setClientCertificate(	$collectiviteProperties->getFilePath('tdt_user_certificat_pem'),
													$collectiviteProperties->getFilePath('tdt_user_key_pem'),
													$collectiviteProperties->get('tdt_user_key_password'));
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
		return $this->exec( self::URL_CLASSIFICATION );
	}
	
	
	public function postActes(DonneesFormulaire $donneesFormulaire) {
		
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
			return false;
		}	
				
		if (! preg_match("/^OK/",$result)){
			$this->lastError = "Erreur lors de la transmission, Tédétis a répondu : $result";
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
		print_r($result);
		$ligne = explode("\n",$result);
		if (trim($ligne[0]) != 'OK'){
			$this->lastError = trim($ligne[1]);
			return false;
		}
		
		return trim($ligne[1]);
	}
}