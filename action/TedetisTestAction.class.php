<?php


class TedetisTestAction extends ActionExecutor {
	
	public function go(){
		$tedetis = TedetisFactory::getInstance($this->getCollectiviteProperties());
		
		$result = $tedetis->testConnexion();
		
		if (! $result){
			$this->setLastMessage("La connexion avec ".$tedetis->getLogicielName()." a échoué : " . $tedetis->getLastError());
			return false;
		}

		$this->setLastMessage("La connexion est réussie : " . $result);
		return true;
	}
	
}