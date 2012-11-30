<?php
require_once( PASTELL_PATH . "/lib/connecteur/Connecteur.class.php");

class OpenSign extends Connecteur {
	
	private $wsdl;
	private $soapClientFactory;
	
	public function __construct($wsdl, SoapClientFactory $soapClientFactory){
		$this->wsdl = $wsdl;
		$this->soapClientFactory = $soapClientFactory;
	}	
	
	public function test(){
		$soapClient = $this->soapClientFactory->getInstance($this->wsdl );
		if (!$soapClient){
			$this->lastError = $this->soapClientFactory->getLastError();
			return false;
		}		
		return $soapClient->wsEcho("Hello World !");
	}
	

	
	
}