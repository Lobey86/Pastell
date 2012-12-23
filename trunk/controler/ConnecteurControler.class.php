<?php
class ConnecteurControler extends PastellControler {
	
	public function hasDroitOnConnecteur($id_ce){
		$connecteur_entite_info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		if (! $connecteur_entite_info) {
			$this->LastError->setLastError("Ce connecteur n'existe pas");
			$this->redirect("/entite/detail.php?page=3");
		}
		$this->hasDroitEdition($connecteur_entite_info['id_e']);		
		return $connecteur_entite_info;
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
		$this->redirect("/entite/detail.php?id_e=$id_e&page=3");
	}
	
	public function doDelete(){
		$recuperateur = new Recuperateur($_POST);
		$id_ce = $recuperateur->getInt('id_ce');
		
		$this->hasDroitOnConnecteur($id_ce);
		$info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		
		$id_used = $this->FluxEntiteSQL->isUsed($info['id_ce']); 
		
		if ($id_used){
			$this->LastError->setLastError("Ce connecteur est utilisé par des flux :  " . implode(", ",$id_used));
			$this->redirect("/connecteur/edition.php?id_ce=$id_ce");
		}
		
		$this->ConnecteurEntiteSQL->delete($id_ce);

		$this->LastMessage->setLastMessage("Le connecteur « {$info['libelle']} » a été supprimé");
		$this->redirect("/entite/detail.php?id_e={$info['id_e']}&page=3");
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
		
		foreach($donneesFormulaire->getOnChangeAction() as $action) {	
			$result = $this->ActionExecutorFactory->executeOnConnecteur($id_ce,$this->Authentification->getId(),$action);
		}
		
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
	
	public function listConnecteur(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$this->hasDroitLecture($id_e);
		$this->id_e = $id_e;
		$this->all_connecteur = $this->ConnecteurEntiteSQL->getAll($id_e);
		$this->render("ConnecteurList");
	}
	
}