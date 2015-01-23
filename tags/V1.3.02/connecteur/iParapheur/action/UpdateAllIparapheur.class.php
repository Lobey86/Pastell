<?php 

class UpdateAllIparapheur extends ActionExecutor {
	
	public function go(){
		$all_connecteur = $this->objectInstancier->ConnecteurEntiteSQL->getAllById("iParapheur");
		$result = array();
		foreach($all_connecteur as $connecteur_info){
			if ($connecteur_info['id_e'] == 0) continue;
			$this->objectInstancier->ActionExecutorFactory->executeOnConnecteur($connecteur_info['id_ce'],$this->id_u,'update-sous-type');
			$result[] = $connecteur_info['id_e'] . " : " . $this->objectInstancier->ActionExecutorFactory->getLastMessage();
		}
		$this->setLastMessage("Résultat :<br/>".implode("<br/>",$result));
		return true;
	}
	
}