<?php 

class DemandeClassificationAll extends ActionExecutor {
	
	public function go(){

		throw new Exception ("Not implemented exception !");
		
		//TODO : il faut pouvoir r�cuperer tous les connecteur S�low de toutes les 
		// 		collectivit�
		$entiteListe = new EntiteListe($this->getSQLQuery());
		
		$all_col = $entiteListe->getAll(Entite::TYPE_COLLECTIVITE);
		$all_col =  array_merge($all_col,$entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION));
		
		$envoye = array();
		foreach($all_col as $infoCollectivite) {
			$donneesFormulaire = $this->getDonneesFormulaireFactory()->getEntiteFormulaire($infoCollectivite['id_e']);
			
			if ($donneesFormulaire->get('tdt_activate')){
				$tedetis = TedetisFactory::getInstance($donneesFormulaire);
				$result = $tedetis->demandeClassification();
				
				if (! $result){
					$envoye[] = $infoCollectivite['denomination'] . " [ERREUR] " .$tedetis->getLastError();
				} else {
					$envoye[] = $infoCollectivite['denomination'] . " - " .$result;
				}
			}
		}
		
		$this->setLastMessage("Demandes envoy�es � <br/>".implode("<br/>",$envoye));
		return true;

	}
	
}