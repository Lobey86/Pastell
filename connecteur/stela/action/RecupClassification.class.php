<?php

require_once(__DIR__."/../Stela.class.php");

class RecupClassification extends ActionExecutor {
	
	public function go(){
		$connecteur_properties = $this->getConnecteurProperties();
		$stela = new Stela($connecteur_properties); 
		
		$classification = $stela->getClassification();
		if (! $classification){
			$this->setLastMessage($stela->getLastError());
			return false;
		}
		
		$connecteur_properties->addFileFromData("classification_file","classification.xml",$classification);
		
		$this->setLastMessage("La classification a été mise à jour");
		return true;
	}
	
}