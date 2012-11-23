<?php 

require_once(PASTELL_PATH . "/lib/system/WebGFC.class.php");


class WebGFCStatus extends ActionExecutor {

	
	public function go(){
		$webgfc_courrier_id = $this->getDonneesFormulaire()->get('webgfc_courrier_id');
		
		
		$webGFC = new WebGFC();
		$result = $webGFC->getStatus($webgfc_courrier_id);
		
		$this->setLastMessage($webGFC->getLastMessage());
		
		if ($result == "OK"){
			
			$documentEntite = new DocumentEntite($this->getSQLQuery());
			
			$id_col = $documentEntite->getEntiteWithRole($this->id_d,"lecteur");
			
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'traite-webgfc', "Le document a été traité ");
			$this->getActionCreator()->addToEntite($id_col,"Le document a été traité");
		}
		
		return true;
	}
	
}