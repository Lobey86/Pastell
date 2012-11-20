<?php

require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class TedetisTestAction extends ActionExecutor {
	
	public function go(){
				
		$tedetis = TedetisFactory::getInstance($this->getDonneesFormulaire());
		
		$result = $tedetis->testConnexion();
		
		if (! $result){
			$this->setLastMessage("La connexion avec ".$tedetis->getLogicielName()." a échoué : " . $tedetis->getLastError());
			return false;
		}

		$this->setLastMessage("La connexion est réussie : " . $result);
		return true;
	}
	
}