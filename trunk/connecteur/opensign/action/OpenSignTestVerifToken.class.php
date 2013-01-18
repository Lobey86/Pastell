<?php
require_once( __DIR__ . "/../OpenSign.class.php");

class OpenSignTestVerifToken extends ActionExecutor {
	
	public function go(){
		$opensign = $this->getMyConnecteur();
		$data = mt_rand(0,mt_getrandmax());
		$token = $opensign->getTimestampReply($data);
		
		$donneesFormulaire = $this->getConnecteurProperties();
		$opensign_ca = $donneesFormulaire->getFilePath("opensign_ca",0);
		$opensign_x509 = $donneesFormulaire->getFilePath("opensign_x509",0);
		
		
		$this->objectInstancier->OpensslTSWrapper->verify($data,$token,$opensign_ca,$opensign_x509);
		$this->setLastMessage($this->objectInstancier->OpensslTSWrapper->getLastError());
		return true;
	}
	
}