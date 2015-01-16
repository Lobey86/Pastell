<?php

class EnvoieDemandeClassification extends ActionExecutor {
	
	public function go(){
		$s2low = $this->getMyConnecteur();
		$result = $s2low->demandeClassification();
		$this->setLastMessage($result);
		return true;
	}
	
}