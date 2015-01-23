<?php
class FournisseurFactureChoixCollectivite extends ChoiceActionExecutor {
	
	
	public function go(){
		$recuperateur = new Recuperateur($_GET);
		$id_e_collectivite = $recuperateur->get('id_e_collectivite');
		
		
		if ( ! $this->objectInstancier->CollectiviteFournisseurSQL->isRelationOk($id_e_collectivite,$this->id_e) ){
			$this->setLastMessage("Vous n'avez pas de relation validée avec ce fournisseur");
			return false;
		}
		
		$collectivite_info = $this->objectInstancier->EntiteSQL->getInfo($id_e_collectivite);
		
		$this->getDonneesFormulaire()->setData('id_e_collectivite', $id_e_collectivite);
		$this->getDonneesFormulaire()->setData('siren_collectivite', $collectivite_info['siren']);
		$this->getDonneesFormulaire()->setData('denomination_collectivite', $collectivite_info['denomination']);
		
		$doc = $this->objectInstancier->DocumentActionEntite->getListDocument($this->id_e,'fournisseur-inscription',0,1);
		if (count($doc)<1){
			$this->setLastMessage("Impossible de trouver les informations du fournisseur");
			return false;
		}
		$donneesFormulaireFournisseurInscription = $this->getDonneesFormulaireFactory()->get($doc[0]['id_d']);
		$this->getDonneesFormulaire()->setData('raison_sociale_fournisseur', $donneesFormulaireFournisseurInscription->get('raison_sociale'));
		$this->getDonneesFormulaire()->setData('siret_fournisseur', $donneesFormulaireFournisseurInscription->get('SIRET'));
		$this->getDonneesFormulaire()->setData('rcs_fournisseur', $donneesFormulaireFournisseurInscription->get('RCS'));
		return true;
	}
	
	public function display(){
		$collectivite_info_list = array();
		foreach( $this->displayAPI() as $id_e){
			$collectivite_info = $this->objectInstancier->EntiteSQL->getInfo($id_e);
			$collectivite_info['link'] ="document/external-data-controler.php?id_e={$this->id_e}&id_d={$this->id_d}&page={$this->page}&field={$this->field}&id_e_collectivite=$id_e"; 
			$collectivite_info_list[] = $collectivite_info;
		}
		$this->collectivite_info_list = $collectivite_info_list;
		$this->renderPage("Choix de la collectivité","FournisseurFactureChoixCollectivite");
	}
	
	public function displayAPI(){
		return $this->objectInstancier->CollectiviteFournisseurSQL->getAllCollectiviteId($this->id_e);
	}
	
}