<?php
class ConnecteurControler extends PastellControler {
	
	public function verifDroitOnConnecteur($id_ce){
		$connecteur_entite_info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		if (! $connecteur_entite_info) {
			$this->LastError->setLastError("Ce connecteur n'existe pas");
			$this->redirect("/entite/detail.php?page=3");
		}
		$this->hasDroitEdition($connecteur_entite_info['id_e']);		
		return $connecteur_entite_info;
	}
	
    //Refactoring de la fonction doNouveau pour appeler la nouvelle function "nouveau"
	public function doNouveau(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$libelle = $recuperateur->get('libelle');
		$id_connecteur = $recuperateur->get('id_connecteur');
		
                try {
                    if ($id_e) {
                        $this->hasDroitEdition($id_e);
                    }                    
                    $this->nouveau($id_e, $id_connecteur, $libelle);
                    $this->lastMessage->setLastMessage("Connecteur ajouté avec succès");                    
                    $this->redirect("/entite/detail.php?id_e=$id_e&page=3");                    
                } catch (Exception $ex) {
                    $this->LastError->setLastError($ex->getMessage());
		    $this->redirect("/connecteur/new.php?id_e=$id_e");
                } 
	}
	
        // Nouvelle méthode commune entre ihm (doNouveau) et API.
        public function nouveau($id_e, $id_connecteur, $libelle) {
            if (!$libelle){
                throw new Exception("Le libellé est obligatoire.");
            }				
		
            if ($id_e){
		$connecteur_info = $this->ConnecteurDefinitionFiles->getInfo($id_connecteur);
            } else {
		$connecteur_info = $this->ConnecteurDefinitionFiles->getInfoGlobal($id_connecteur);
            }
            if (!$connecteur_info){
		throw new Exception("Aucun connecteur de ce type.");	
            } else {
		return $this->ConnecteurEntiteSQL->addConnecteur($id_e,$id_connecteur,$connecteur_info['type'],$libelle);
            }
        }
        
        
        //Refactoring de la methode doDelete()
	public function doDelete(){
		$recuperateur = new Recuperateur($_POST);
		$id_ce = $recuperateur->getInt('id_ce');
		
		$this->verifDroitOnConnecteur($id_ce);
		try {                    
                    $info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
                    $this->delete($id_ce);
                    $this->LastMessage->setLastMessage("Le connecteur « {$info['libelle']} » a été supprimé.");
                    $this->redirect("/entite/detail.php?id_e={$info['id_e']}&page=3");
                } catch (Exception $ex) {
                    $this->LastError->setLastError($ex->getMessage());
                    $this->redirect("/connecteur/edition.php?id_ce=$id_ce");
                }                                
	}
        
	//Nouvelle méthode "delete" commune entre ihm(doDelete) et API.
        public function delete($id_ce) {
            
            $info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
            if (!$info) {
                throw new Exception("Ce connecteur n'existe pas.");
            }
	    $id_used = $this->FluxEntiteSQL->isUsed($info['id_ce']); 
		
            if ($id_used){
                throw new Exception("Ce connecteur est utilisé par des flux :  " . implode(", ",$id_used));		
	    }
            //Suppression des fichiers sur le disque.
            $donneesFormulaire = $this->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
            $donneesFormulaire->delete();
            // Suppression du connecteur de la bdd
            $this->ConnecteurEntiteSQL->delete($id_ce);
        }
        
        
        //Refactoring de la methode doEditionLibelle
	public function doEditionLibelle(){
		$recuperateur = new Recuperateur($_POST);
		$id_ce = $recuperateur->getInt('id_ce');
		$libelle = $recuperateur->get('libelle');
		$this->verifDroitOnConnecteur($id_ce);
		
                try {
                    $this->editionLibelle($id_ce, $libelle);
                }catch (Exception $ex) {
                    $this->LastError->setLastError($ex->getMessage());
                    $this->redirect("/connecteur/edition-libelle.php?id_ce=$id_ce");                    
                }
		$this->LastMessage->setLastMessage("Le connecteur « $libelle » a été modifié.");
		$this->redirect("/connecteur/edition.php?id_ce=$id_ce");
	}
	
        
        //Nouvelle méthode commune en IHM (doEditionLibelle) et API.
        public function editionLibelle($id_ce, $libelle) {            
            $info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
            if (!$info) {
                throw new Exception("Ce connecteur n'existe pas.");
            }            
            if (!$libelle) {
                throw new Exception ("Le libellé est obligatoire.");
            }            
            $this->ConnecteurEntiteSQL->edit($id_ce,$libelle);
        }
        
        
	public function doEditionModif(){
		$recuperateur = new Recuperateur($_POST);
		$id_ce = $recuperateur->getInt('id_ce');
		$this->verifDroitOnConnecteur($id_ce);
		
		$fileUploader = new FileUploader();
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
		
		$this->verifDroitOnConnecteur($id_ce);

		$donneesFormulaire = $this->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
		$filePath = $donneesFormulaire->getFilePath($field,$num);
		if (!$filePath){
			$this->LastError->setLastError("Ce fichier n'existe pas");
			$this->redirect("/connecteur/edition.php?id_ce=$id_ce");
		}
		$fileName = $donneesFormulaire->getFileName($field,$num);
		
		header("Content-type: ".mime_content_type($filePath));
		header("Content-disposition: attachment; filename=\"$fileName\"");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		readfile($filePath);
	}
	
	public function deleteFile(){
		$recuperateur = new Recuperateur($_GET);
		$id_ce = $recuperateur->getInt('id_ce');
		$field = $recuperateur->get('field');
		$num = $recuperateur->getInt('num');
		
		$this->verifDroitOnConnecteur($id_ce);
		
		$donneesFormulaire = $this->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
		$donneesFormulaire->removeFile($field,$num);
		
		$this->redirect("/connecteur/edition-modif.php?id_ce=$id_ce");
	}
	

	
	public function deleteAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_ce = $recuperateur->getInt('id_ce');
		$this->verifDroitOnConnecteur($id_ce);		
		$this->connecteur_entite_info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		
		$this->page_title = "Suppression du connecteur  « {$this->connecteur_entite_info['libelle']} »";
		$this->template_milieu = "ConnecteurDelete";
		$this->renderDefault();
	}
	
	
	private function setConnecteurInfo(){
		$recuperateur = new Recuperateur($_GET);
		$id_ce = $recuperateur->getInt('id_ce');
		
		$this->verifDroitOnConnecteur($id_ce);
		
		$connecteur_entite_info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		$id_e = $connecteur_entite_info['id_e'];
		$entite_info = $this->EntiteSQL->getInfo($id_e);
		
		$donneesFormulaire = $this->donneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
		
		$this->inject = array('id_e'=>$id_e,'id_ce'=>$id_ce,'id_d'=>'','action'=>'');
		
		$this->my_role = "";
		
		$this->donneesFormulaire = $donneesFormulaire;
		
		if ($connecteur_entite_info['id_e']){
			$this->action = $this->DocumentTypeFactory->getEntiteDocumentType($connecteur_entite_info['id_connecteur'])->getAction();
		} else {
			$this->action = $this->DocumentTypeFactory->getGlobalDocumentType($connecteur_entite_info['id_connecteur'])->getAction();
		} 
		
		
		if (! $id_e){
			$entite_info['denomination'] = "Entité racine";
		}
		$this->entite_info = $entite_info;
		$this->connecteur_entite_info = $connecteur_entite_info;
		$this->id_ce = $id_ce;
		$this->id_e = $id_e;
	}
	public function editionModif(){
		$this->setConnecteurInfo();
		$this->page_title = "Configuration des connecteurs pour « {$this->entite_info['denomination']} »";
		$this->action_url = "connecteur/edition-modif-controler.php";
		$this->recuperation_fichier_url = "connecteur/recuperation-fichier.php?id_ce=".$this->id_ce;
		$this->suppression_fichier_url = "connecteur/supprimer-fichier.php?id_ce=".$this->id_ce;
		$this->page = 0;
		$this->externalDataURL = "connecteur/external-data.php" ;
		
		$this->template_milieu = "ConnecteurEditionModif";
		$this->renderDefault();
	}
	
	public function editionAction(){
		$this->setConnecteurInfo();
		$this->page_title = "Configuration des connecteurs pour « {$this->entite_info['denomination']} »";
		$this->recuperation_fichier_url = "connecteur/recuperation-fichier.php?id_ce=".$this->id_ce;
		$this->template_milieu = "ConnecteurEdition";
		
		$this->renderDefault();
	}
	
	public function newAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e');
		$this->verifDroit($id_e, "entite:edition");
		
		$this->id_e = $id_e;		
		$this->all_connecteur_dispo = $this->ConnecteurDefinitionFiles->getAllByIdE($id_e);
		
		$this->page_title = "Ajout d'un connecteur";
		$this->template_milieu = "ConnecteurNew";
		$this->renderDefault();
	}
	
	public function editionLibelleAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_ce = $recuperateur->getInt('id_ce');
		
		$this->verifDroitOnConnecteur($id_ce);
		
		$this->connecteur_entite_info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		
		$this->page_title = "Modification du connecteur  « {$this->connecteur_entite_info['libelle']} »";
		$this->template_milieu = "ConnecteurEditionLibelle";
		$this->renderDefault();
		
	}
	
}