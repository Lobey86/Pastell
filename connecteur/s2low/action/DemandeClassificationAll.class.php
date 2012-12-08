<?php 

class DemandeClassificationAll extends ActionExecutor {
	
	public function go(){

		$entiteListe = new EntiteListe($this->getSQLQuery());
		
		$all_col = $entiteListe->getAll(Entite::TYPE_COLLECTIVITE);
		$all_col =  array_merge($all_col,$entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION));
		
		$envoye = array();
		foreach($all_col as $infoCollectivite) {			
			try {
				$tdT = $this->objectInstancier->ConnecteurFactory->getConnecteurByType($infoCollectivite['id_e'],'actes','TdT');
				$result = $tdT->demandeClassification();
				$envoye[] = "{$infoCollectivite['denomination']}  : demande de classification envoyée";
			} catch(Exception $e ){
				$envoye[] = "{$infoCollectivite['denomination']}  : ".($e->getMessage());
				continue;
			}
		}
		
		$this->setLastMessage("Demandes envoyées à <br/>".implode("<br/>",$envoye));
		return true;
	}
	
}