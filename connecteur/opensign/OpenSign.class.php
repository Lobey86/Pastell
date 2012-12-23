<?php

require_once(__DIR__."/../../connecteur-type/Horodateur.class.php");

class OpenSignException extends ConnecteurException {}

class OpenSign extends Horodateur {
	
	private $wsdl;
	private $soapClientFactory;
	
	public function __construct(OpensslTSWrapper $opensslTSWrapper, SoapClientFactory $soapClientFactory){
		parent::__construct($opensslTSWrapper);
		$this->soapClientFactory = $soapClientFactory;
	}	
	
	public function setConnecteurConfig(DonneesFormulaire $donnesFormulaire){
		$this->wsdl = $donnesFormulaire->get('opensign_wsdl');
	}
	
	public function getTimestampReply($data){
		$timestampRequest = $this->opensslTSWrapper->getTimestampQuery($data);
		$token = $this->getToken($timestampRequest);
		return $token;
	}
	
	public function test(){
		$soapClient = $this->getSoapClient();
		return $soapClient->wsEcho("Hello World !");
	}
	
	private function getSoapClient(){
		$soapClient = $this->soapClientFactory->getInstance($this->wsdl);
		return $soapClient;
	}
	
	private function getToken($timestampRequest){		
		$soapClient = $this->getSoapClient();
		$response = $soapClient->createResponse( array('request'=> base64_encode($timestampRequest)));
	    if (!$response){
	    	throw new OpenSignException("Impossible de récuperer le token");
	    }
		return base64_decode($response);
	}
	
	
}