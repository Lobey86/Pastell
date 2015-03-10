<?php 

class RecupClassificationAll extends ActionExecutor {
	
	public function go(){

		$entiteListe = new EntiteListe($this->getSQLQuery());
		
		$all_col = $entiteListe->getAll(Entite::TYPE_COLLECTIVITE);
		$all_col =  array_merge($all_col,$entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION));
		
		$envoye = array();
		foreach($all_col as $infoCollectivite) {			
			try {
				$tdT = $this->objectInstancier->ConnecteurFactory->getConnecteurByType($infoCollectivite['id_e'],'actes-generique','TdT');
				if (!$tdT){
					continue;
				}
				$classification = $tdT->getClassification();
				$connecteur_properties = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($infoCollectivite['id_e'],'actes-generique','TdT');
				$connecteur_properties->addFileFromData("classification_file","classification.xml",$classification);
				
				$envoye[] = "{$infoCollectivite['denomination']}  : classification récupérée";
			} catch(Exception $e ){
				$envoye[] = "{$infoCollectivite['denomination']}  : ".($e->getMessage());
				continue;
			}
		}
		
		$this->setLastMessage("Résultat :<br/>".implode("<br/>",$envoye));
		return true;
	}
	
}