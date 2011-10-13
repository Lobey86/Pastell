<?php

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class IParapheurTest extends ActionExecutor {
	
	public function go(){
			
		$iParapheur = new IParapheur($this->getDonneesFormulaire());
		$result = $iParapheur->testConnexion();
		
		
		
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}

		$this->setLastMessage("La connexion est réussie : ".$result);
		return true;
	}
	
}