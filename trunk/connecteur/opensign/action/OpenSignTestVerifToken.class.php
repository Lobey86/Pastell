<?php
require_once( __DIR__ . "/../OpenSign.class.php");

class OpenSignTestVerifToken extends ActionExecutor {
	
	public function go(){		
		$opensign = $this->getMyConnecteur();
		$data = mt_rand(0,mt_getrandmax());
		$token = $opensign->getTimestampReply($data);		
		$opensign->verify($data,$token);
		$this->setLastMessage("Vérification: OK");
		return true;
	}
	
}