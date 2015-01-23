<?php

class CreationDocumentRecupAuto extends ActionExecutor {

	public function go(){
		$connecteur = $this->getMyConnecteur();
		$result = $connecteur->recupAllAuto($this->id_e);
		if ($result){
			$this->setLastMessage(implode("<br/>",$result));
		} else {
			$this->setLastMessage("Aucun fichier trouvé");
		}
		return true;
	}

}