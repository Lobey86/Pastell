<?php

class AccepterModerateur extends ActionExecutor {

	public function go(){
		$documentEntite = new DocumentEntite($this->getSQLQuery());
		$id_fournisseur = $documentEntite->getEntiteWithRole($this->id_d,"editeur");

		$all_entite = $documentEntite->getEntite($this->id_d);
		
		foreach($all_entite as $document_entite){
			if($document_entite['role'] == 'lecteur' && $document_entite['last_action'] == 'accepter-collectivite'){
				$this->openService($document_entite['id_e'],$id_fournisseur);
			}	
		}
		
		$this->setLastMessage("Le service à été ouvert entre le fournisseur et la ou les collectivités qui ont accepté celui-ci");
		
		return true;
	}

	/**
	 * @return CollectiviteFournisseurSQL
	 */
	private function getCollectiviteFournisseurSQL(){
		return $this->objectInstancier->CollectiviteFournisseurSQL;
	}
	
	public function openService($id_e_col,$id_fournisseur){
		$collectivite_fournisseur_info = $this->getCollectiviteFournisseurSQL()->getInfo($id_e_col, $id_fournisseur);
		if ($collectivite_fournisseur_info['is_valid']){
			return;
		}
		$this->getCollectiviteFournisseurSQL()->validRelation($id_e_col, $id_fournisseur);
		
		$collectivite_info = $this->objectInstancier->EntiteSQL->getInfo($id_e_col);
		$fournisseur_info = $this->objectInstancier->EntiteSQL->getInfo($id_fournisseur);
		
		$message = "Ouverture du service entre {$fournisseur_info['denomination']} et {$collectivite_info['denomination']}";
		$actionCreator = $this->getActionCreator();
		$actionCreator->addAction($this->id_e,$this->id_u,$this->action,$message);
		$actionCreator->addToEntite($id_fournisseur,$message);
		$actionCreator->addToEntite($id_e_col,$message);
		$this->notify($this->action, $this->type, $message);
	}
	
}