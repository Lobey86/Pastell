<?php

require_once(__DIR__."/../S2low.class.php");


class EnvoieDemandeClassification extends ActionExecutor {
	
	public function go(){
		$connecteur_properties = $this->getConnecteurProperties();
		$s2low = new S2low($connecteur_properties); 
		$result = $s2low->demandeClassification();
		$this->setLastMessage($result);
		return true;
	}
	
}