<?php
class ConnecteurControler extends Controler {
	
	
	public function hasDroitEdition($id_e){
		$droit_ecriture = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),
															"entite:edition",$id_e);
		if ( ! $droit_ecriture ){
			$this->LastError->setLastError("Vous n'avez pas le droit d'édition sur cette entité");
			$this->redirect("/entite/detail.php?id_e=$id_e&page=2");
		}

		if ( $id_e && ! $this->EntiteSQL->getInfo($id_e)){
			$this->LastError->setLastError("L'entité $id_e n'existe pas");
			$this->redirect("/entite/index.php");
		}
	}
	
	public function hasDroitOnConnecteur($id_ce){
		$connecteur_entite_info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		if (! $connecteur_entite_info) {
			$this->LastError->setLastError("Ce connecteur n'existe pas");
			$this->redirect("/entite/index.php");
		}
		$this->hasDroitEdition($connecteur_entite_info['id_e']);		
	}
	
	public function doNouveau(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$libelle = $recuperateur->get('libelle');
		$id_connecteur = $recuperateur->get('id_connecteur');
		
		$this->hasDroitEdition($id_e);
		
		if ($id_e){
			$connecteur_info = $this->ConnecteurDefinitionFiles->getInfo($id_connecteur);
		} else {
			$connecteur_info = $this->ConnecteurDefinitionFiles->getInfoGlobal($id_connecteur);
		}
		
		if (!$connecteur_info){
			$this->lastError->setLastError("Aucun connecteur de ce type.");	
		} else {
			$this->ConnecteurEntiteSQL->addConnecteur($id_e,$id_connecteur,$connecteur_info['type'],$libelle);
			$this->lastMessage->setLastMessage("Connecteur ajouté avec succès");
		}
		$this->redirect("/entite/detail.php?id_e=$id_e&page=2");
	}
	
	public function doDelete(){
		$recuperateur = new Recuperateur($_POST);
		$id_ce = $recuperateur->getInt('id_ce');
		
		$this->hasDroitOnConnecteur($id_ce);
		$info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		
		$this->ConnecteurEntiteSQL->delete($id_ce);

		$this->LastMessage->setLastMessage("Le connecteur « {$info['libelle']} » a été supprimé");
		$this->redirect("/entite/detail.php?id_e={$info['id_e']}&page=2");
	}
	
	public function doEditionLibelle(){
		$recuperateur = new Recuperateur($_POST);
		$id_ce = $recuperateur->getInt('id_ce');
		$libelle = $recuperateur->get('libelle');
		$this->hasDroitOnConnecteur($id_ce);
		
		$this->ConnecteurEntiteSQL->edit($id_ce,$libelle);

		$this->LastMessage->setLastMessage("Le connecteur « $libelle » a été modifié");
		$this->redirect("/connecteur/edition.php?id_ce=$id_ce");
	}
	
	public function doEditionModif(){
		$recuperateur = new Recuperateur($_POST);
		$id_ce = $recuperateur->getInt('id_ce');
		$this->hasDroitOnConnecteur($id_ce);
		$fileUploader = new FileUploader($_FILES);
		$donneesFormulaire = $this->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
		
		$donneesFormulaire->saveTab($recuperateur,$fileUploader,0);
		$this->redirect("/connecteur/edition.php?id_ce=$id_ce");
	}
	
	public function recupFile(){
		$recuperateur = new Recuperateur($_GET);
		$id_ce = $recuperateur->getInt('id_ce');
		$field = $recuperateur->get('field');
		$num = $recuperateur->getInt('num');
		
		$this->hasDroitOnConnecteur($id_ce);

		$donneesFormulaire = $this->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
		if ( ! $donneesFormulaire->sendFile($field,$num)){
			$this->LastError->setLastError($donneesFormulaire->getLastError());
			$this->redirect("/connecteur/edition.php?id_ce=$id_ce");
		}
	}
	
	public function deleteFile(){
		$recuperateur = new Recuperateur($_GET);
		$id_ce = $recuperateur->getInt('id_ce');
		$field = $recuperateur->get('field');
		$num = $recuperateur->getInt('num');
		
		$this->hasDroitOnConnecteur($id_ce);
		
		$donneesFormulaire = $this->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
		$donneesFormulaire->removeFile($field,$num);
		
		$this->redirect("/connecteur/edition-modif.php?id_ce=$id_ce");
	}
	
}