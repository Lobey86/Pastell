<?php
require_once( __DIR__ . "/../OpenSign.class.php");

class OpenSignTest extends ActionExecutor {
	
	public function go(){
		$opensign = new OpenSign($this->getConnecteurProperties()->get('opensign_wsdl'), $this->objectInstancier->SoapClientFactory);
		$result = $opensign->test();
		if (! $result ){
			$this->setLastMessage($opensign->getLastError());
			return false;	
		}
		$this->setLastMessage("Connexion OpenSign OK: $result");
		return true;
	}
	
}