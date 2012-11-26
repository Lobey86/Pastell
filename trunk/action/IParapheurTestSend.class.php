<?php
require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

class IParapheurTestSend extends ActionExecutor {
	
	public function go(){
			
		$iParapheur = new IParapheur($this->getCollectiviteProperties());
		$result = $iParapheur->sendDocumentTest();
		
		
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}

		$this->setLastMessage($result);
		return true;
	}
	
}