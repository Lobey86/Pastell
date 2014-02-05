<?php

class RecupClassification extends ActionExecutor {
	
	public function go(){
		$s2low = $this->getMyConnecteur();
		$classification = $s2low->getClassification();
		$this->getConnecteurProperties()->addFileFromData("classification_file","classification.xml",$classification);
		$this->setLastMessage("La classification a été mise à jour");
		return true;
	}
	
}