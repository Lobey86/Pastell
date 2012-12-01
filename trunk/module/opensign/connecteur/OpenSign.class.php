<?php
require_once( PASTELL_PATH . "/lib/connecteur/Connecteur.class.php");

class OpenSign extends Connecteur {
	
	private $wsdl;
	private $soapClientFactory;
	
	public function __construct($wsdl, SoapClientFactory $soapClientFactory){
		$this->wsdl = $wsdl;
		$this->soapClientFactory = $soapClientFactory;
	}	
	
	private function getSoapClient(){
		$soapClient = $this->soapClientFactory->getInstance($this->wsdl);
		if (!$soapClient){
			$this->lastError = $this->soapClientFactory->getLastError();
			return false;
		}
		return $soapClient;
	}
	
	public function test(){
		$soapClient = $this->getSoapClient();
		if (!$soapClient){
			return false;
		}
		return $soapClient->wsEcho("Hello World !");
	}

	//$timestampRequest : raw timestamp request in binary format, not base64 encoded !
	public function getToken($timestampRequest){		
		$soapClient = $this->getSoapClient();
		if (!$soapClient){
			return false;
		}
	    try {			
		    $response = $soapClient->createResponse( array('request'=> base64_encode($timestampRequest)));
	    } catch (Exception $e){
	    	$this->lastError = $e->getMessage();
	    	return false;
	    }
	    if (!$response){
	    	$this->lastError = "Impossible de récuperer le token";
	    	return false;
	    }
		return base64_decode($response);
	}
	
	
}