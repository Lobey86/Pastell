<?php 

require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once(PASTELL_PATH . "/lib/system/WebGFC.class.php");


class StatusWebGFC extends ActionExecutor {

	
	public function go(){
		$webgfc_courrier_id = $this->getDonneesFormulaire()->get('webgfc_courrier_id');
		
		
		$webGFC = new WebGFC();
		$result = $webGFC->getStatus($webgfc_courrier_id);
		
		$this->setLastMessage($webGFC->getLastMessage());
		
		if ($result == "OK"){
			
			/*$entiteCollectivite = new Entite($this->getSQLQuery(),$id_col);
			$infoCollectivite = $entiteCollectivite->getInfo();
			$denomination_col = $infoCollectivite['denomination'];*/
			
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'traite-webgfc', "Le document a été traité ");
			//$this->getActionCreator()->addToEntite($id_col,"Le document a été envoyé par $emmeteurName");
		}
		
		return true;
	}
	
}