<?php

class IParapheurTestSend extends ActionExecutor {
	
	public function go(){
			
		$iParapheur = $this->getMyConnecteur();
		
		$result = $iParapheur->sendDocumentTest();
		
		
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}

		$this->setLastMessage($result);
		return true;
	}
	
}