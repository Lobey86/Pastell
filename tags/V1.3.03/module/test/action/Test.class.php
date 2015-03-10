<?php
class Test extends ActionExecutor {
		
	public function go(){
		$df = $this->getDonneesFormulaire();
		$this->setLastMessage($df->get('password'));
		return true;
	}
	
}