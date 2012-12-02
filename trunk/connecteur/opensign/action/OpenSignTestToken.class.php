<?php
require_once( __DIR__ . "/../OpenSign.class.php");

class OpenSignTestToken extends ActionExecutor {
	
	public function go(){
		$opensign = new OpenSign($this->getConnecteurProperties()->get('opensign_wsdl'), $this->objectInstancier->SoapClientFactory);
		$timestampRequest = $this->objectInstancier->OpensslTSWrapper->getTimestampQuery(mt_rand(0,mt_getrandmax()));
			
		$result = $opensign->getToken($timestampRequest);
		if (! $result ){
			$this->setLastMessage($opensign->getLastError());
			return false;	
		}
		
		$result = $this->objectInstancier->OpensslTSWrapper->getTimestampReplyString($result);
		
		$this->setLastMessage("Connexion OpenSign OK: <br/><br/>" . nl2br($result));
		return true;
	}
	
}