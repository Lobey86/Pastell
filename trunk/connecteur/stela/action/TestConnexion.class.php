<?php

require_once(__DIR__."/../Stela.class.php");

class TestConnexion extends ActionExecutor {
	
	public function go(){
		
		$connecteur_properties = $this->getConnecteurProperties();
		
		$stela = new Stela($connecteur_properties); 
		$result = $stela->testConnexion();
		
		if (! $result){
			$this->setLastMessage("La connexion avec Stela a échoué : " . $stela->getLastError());
			return false;
		}

		$this->setLastMessage("La connexion est réussie : " . $result);
		return true;
	}
	
}