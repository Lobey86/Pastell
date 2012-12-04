<?php

class OpenSignException extends ConnecteurException {}

class OpenSign extends Connecteur {
	
	private $wsdl;
	private $soapClientFactory;
	
	public function __construct(SoapClientFactory $soapClientFactory){
		$this->soapClientFactory = $soapClientFactory;
	}	
	
	public function setConnecteurConfig(DonneesFormulaire $donnesFormulaire){
		$this->wsdl = $donnesFormulaire->get('opensign_wsdl');
	}
	
	
	private function getSoapClient(){
		$soapClient = $this->soapClientFactory->getInstance($this->wsdl);
		return $soapClient;
	}
	
	public function test(){
		$soapClient = $this->getSoapClient();
		return $soapClient->wsEcho("Hello World !");
	}

	//$timestampRequest : raw timestamp request in binary format, not base64 encoded !
	public function getToken($timestampRequest){		
		$soapClient = $this->getSoapClient();
		$response = $soapClient->createResponse( array('request'=> base64_encode($timestampRequest)));
	    if (!$response){
	    	throw new OpenSignException("Impossible de récuperer le token");
	    }
		return base64_decode($response);
	}
	
	
}