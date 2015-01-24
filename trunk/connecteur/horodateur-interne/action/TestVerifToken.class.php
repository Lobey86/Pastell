<?php

class TestVerifToken extends ActionExecutor {
	
	public function go(){
		$horodateur = $this->getMyConnecteur();
		$data = mt_rand(0,mt_getrandmax());
		$token = $horodateur->getTimestampReply($data);
		
		
		
		$horodateur->verify($data,$token);
		$this->setLastMessage("Vérification: OK");
		return true;		
	}
	
}