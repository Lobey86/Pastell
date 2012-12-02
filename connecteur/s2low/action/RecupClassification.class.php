<?php

require_once(__DIR__."/../S2low.class.php");

class RecupClassification extends ActionExecutor {
	
	public function go(){
		$connecteur_properties = $this->getConnecteurProperties();
		$s2low = new S2low($connecteur_properties); 
		
		$classification = $s2low->getClassification();
		if (! $classification){
			$this->setLastMessage($s2low->getLastError());
			return false;
		}
		
		$connecteur_properties->addFileFromData("classification_file","classification.xml",$classification);
		
		$this->setLastMessage("La classification a été mise à jour");
		return true;
	}
	
}