<?php

require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class TedetisRecupClassification extends ActionExecutor {
	
	public function go(){
				
		$tedetis = new Tedetis($this->getDonneesFormulaire());
		$result = $tedetis->getClassification();
		
		if (! $result){
			$this->setLastMessage($tedetis->getLastError());
			return false;
		}
		
		$this->getDonneesFormulaire()->addFileFromData("classification_file","classification.xml",$result);
		
		$this->setLastMessage("La classification a été mise à jour");
		return true;

	}
	
}