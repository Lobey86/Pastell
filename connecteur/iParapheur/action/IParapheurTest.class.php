<?php

class IParapheurTest extends ActionExecutor {
	
	public function go(){
			
		$iParapheur = $this->getMyConnecteur();
		
		$result = $iParapheur->testConnexion();
		
		
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}

		$this->setLastMessage("La connexion est réussie : ".$result);
		return true;
	}
	
}