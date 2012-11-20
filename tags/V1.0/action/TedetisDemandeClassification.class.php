<?php
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class TedetisDemandeClassification extends ActionExecutor {
	
	public function go(){
				
		$tedetis = TedetisFactory::getInstance($this->getDonneesFormulaire());
		$result = $tedetis->demandeClassification();
		
		if (! $result){
			$this->setLastMessage($tedetis->getLastError());
			return false;
		}
				
		$this->setLastMessage($result);
		return true;

	}
	
}