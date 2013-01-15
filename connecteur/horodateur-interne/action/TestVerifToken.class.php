<?php

class TestVerifToken extends ActionExecutor {
	
	public function go(){
		$horodateur = $this->getMyConnecteur();
		$data = mt_rand(0,mt_getrandmax());
		
		$properties = $this->getConnecteurProperties();
		$signer_certificate = $properties->getFilePath("signer_certificate");
		$ca_certificate = $properties->getFilePath("ca_certificate");
		$token = $horodateur->getTimestampReply($data);
		$this->objectInstancier->OpensslTSWrapper->verify($data,$token,$ca_certificate,$signer_certificate);
		$this->setLastMessage($this->objectInstancier->OpensslTSWrapper->getLastError());
		return false;
	}
	
}