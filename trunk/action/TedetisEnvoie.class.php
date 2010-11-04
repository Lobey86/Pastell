<?php
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");



class TedetisEnvoie  extends ActionExecutor {

	public function go(){
		$collectiviteProperties = $this->getDonneesFormulaireFactory()->get($this->id_e,'collectivite-properties');
		
		
		$tedetis = new Tedetis($collectiviteProperties);
		
		if (!  $tedetis->postActes($this->getDonneesFormulaire()) ){
			$this->setLastMessage( $tedetis->getLastError());
			return false;
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au contrôle de légalité");
			
		
		$this->setLastMessage("Le document a été envoyé au contrôle de légalité");
		return true;			
	}
}