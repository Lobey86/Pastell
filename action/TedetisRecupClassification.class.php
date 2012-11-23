<?php


class TedetisRecupClassification extends ActionExecutor {
	
	public function go(){
				
		$tedetis = TedetisFactory::getInstance($this->getDonneesFormulaire());
		
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