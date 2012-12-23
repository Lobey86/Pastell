<?php

class TestToken extends ActionExecutor {
	
	public function go(){
		$horodateur = $this->getMyConnecteur();
		
		$token = $horodateur->getTimestampReply(mt_rand(0,mt_getrandmax()));
		$token_text = $this->objectInstancier->OpensslTSWrapper->getTimestampReplyString($token);
		if (!$token_text){
			throw new Exception("Le token de retour est vide");
		}
		$this->setLastMessage("Connexion OpenSign OK: <br/><br/>" . nl2br($token_text));
		return true;
	}
	
}