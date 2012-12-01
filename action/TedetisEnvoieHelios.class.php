<?php
require_once(PASTELL_PATH."/lib/connecteur/tedetis/TedetisFactory.class.php");

class TedetisEnvoieHelios  extends ActionExecutor {

	public function go(){
			
		$collectiviteProperties = $this->getCollectiviteProperties();
		
		
		$tedetis = TedetisFactory::getInstance($collectiviteProperties);
				
		if (!  $tedetis->postHelios($this->getDonneesFormulaire()) ){
			$this->setLastMessage( $tedetis->getLastError());
			return false;
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au TdT");
			
		
		$this->setLastMessage("Le document a été envoyé au TdT");
		return true;			
	}
}