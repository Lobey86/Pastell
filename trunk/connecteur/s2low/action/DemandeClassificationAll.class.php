<?php 

class DemandeClassificationAll extends ActionExecutor {
	
	public function go(){

		throw new Exception ("Not implemented exception !");
		
		//TODO : il faut pouvoir récuperer tous les connecteur S²low de toutes les 
		// 		collectivité
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
		
		$this->setLastMessage("Demandes envoyées à <br/>".implode("<br/>",$envoye));
		return true;

	}
	
}