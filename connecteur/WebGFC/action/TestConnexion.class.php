<?php

require_once(__DIR__."/../WebGFC.class.php");

class TestConnexion extends ActionExecutor {
	
	public function go(){
		
		$connecteur_properties = $this->getConnecteurProperties();
		
		$webGFC = new WebGFC($connecteur_properties); 
		$result = $webGFC->echoTest("Test de connection");
		
		if (! $result){
			$this->setLastMessage("La connexion avec WebGFC a échoué : " . $webGFC->getLastError());
			return false;
		}

		$this->setLastMessage("La connexion est réussie : " . $result);
		return true;
	}
	
}