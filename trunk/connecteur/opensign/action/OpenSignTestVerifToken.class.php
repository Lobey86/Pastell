<?php
require_once( __DIR__ . "/../OpenSign.class.php");

class OpenSignTestVerifToken extends ActionExecutor {
	
	public function go(){
		$opensign = $this->getMyConnecteur();
		
		$data = mt_rand(0,mt_getrandmax());
		$timestampRequest = $this->objectInstancier->OpensslTSWrapper->getTimestampQuery($data);
			
		$result = $opensign->getToken($timestampRequest);
	
		$this->objectInstancier->OpensslTSWrapper->verify($data,$result,false,false);
		
		$this->setLastMessage($this->objectInstancier->OpensslTSWrapper->getLastError());
		return false;
		
		
	}
	
}