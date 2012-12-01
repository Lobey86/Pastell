<?php
require_once( __DIR__ . "/../connecteur/OpenSign.class.php");

class OpenSignTestVerifToken extends ActionExecutor {
	
	public function go(){
		
		$data = mt_rand(0,mt_getrandmax());
		$opensign = new OpenSign($this->getCollectiviteProperties()->get('opensign_wsdl'), $this->objectInstancier->SoapClientFactory);
		$timestampRequest = $this->objectInstancier->OpensslTSWrapper->getTimestampQuery($data);
			
		$result = $opensign->getToken($timestampRequest);
		if (! $result ){
			$this->setLastMessage($opensign->getLastError());
			return false;	
		}
		

		$this->objectInstancier->OpensslTSWrapper->verify($data,$result,false,false);
		
		$this->setLastMessage($this->objectInstancier->OpensslTSWrapper->getLastError());
		return false;
		
		
	}
	
}