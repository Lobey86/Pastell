<?php


class RecupClassification extends ActionExecutor {
	
	public function go(){
		$stela = $this->getMyConnecteur();
		$classification = $stela->getClassification();
		$this->getConnecteurProperties()->addFileFromData("classification_file","classification.xml",$classification);
		
		$this->setLastMessage("La classification a été mise à jour");
		return true;
	}
	
}