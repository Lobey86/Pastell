<?php
require_once( __DIR__ . "/../OpenSign.class.php");

class OpenSignTestToken extends ActionExecutor {
	
	public function go(){
		$opensign = $this->getMyConnecteur();
		
		$timestampRequest = $this->objectInstancier->OpensslTSWrapper->getTimestampQuery(mt_rand(0,mt_getrandmax()));
			
		$token = $opensign->getToken($timestampRequest);
		$token_text = $this->objectInstancier->OpensslTSWrapper->getTimestampReplyString($token);
		
		$this->setLastMessage("Connexion OpenSign OK: <br/><br/>" . nl2br($token_text));
		return true;
	}
	
}