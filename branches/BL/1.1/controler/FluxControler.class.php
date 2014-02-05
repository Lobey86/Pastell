<?php
class FluxControler extends PastellControler {
	
	public function getConnecteurDispo($id_e,$type_connecteur){
		$this->hasDroitEdition($id_e);

		$connecteur_disponible = $this->ConnecteurEntiteSQL->getDisponible($id_e,$type_connecteur);
		if (! $connecteur_disponible){
			$this->LastError->setLastError("Aucun connecteur « $type_connecteur » disponible !");
			$this->redirect("/entite/detail.php?id_e=$id_e&page=3");
		}
		
		return $connecteur_disponible;
	}
	
        // Refactoring : exception au lieu de la redirection
	public function hasGoodType($id_ce,$type){
		$info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		if ($info['type'] != $type){
                    throw new Exception("Le connecteur n'est pas du bon type.");
//			$this->LastError->setLastError("Le connecteur n'est pas du bon type");
//			$this->redirect("/entite/detail.php?id_e=$id_e&page=3");
		}
	}
        
	// Refactoring - Nouvelle méthode        
        public function redirectWhenError($id_e) {
            $this->redirect("/entite/detail.php?id_e=$id_e&page=3");
        }
        
        // Refactoring
	public function doEditionModif(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$flux = $recuperateur->get('flux');
		$type = $recuperateur->get('type');
		$id_ce = $recuperateur->get('id_ce');

		$this->hasDroitEdition($id_e);
		try {
                    $this->hasGoodType($id_ce, $type);
                    $this->editionModif($id_e, $flux, $type, $id_ce);
                    $this->LastMessage->setLastMessage("Connecteur associé au flux avec succès");
                    $this->redirect("/entite/detail.php?id_e=$id_e&page=4");
                } catch (Exception $ex) {
                    $this->LastError->setLastError($ex->getMessage());
                    $this->redirectWhenError($id_e);
                }                                
	}                
	
        // Nouvelle methode commune IHM et API
        public function editionModif($id_e, $flux, $type, $id_ce) {
            
            $this->hasGoodType($id_ce, $type);
            if ($flux!=null) {
                $info = $this->FluxDefinitionFiles->getInfo($flux);
                if (!$info) {
                    throw new Exception("Le type de flux n'existe pas.");
                }              
            }
            $id_fe = $this->FluxEntiteSQL->addConnecteur($id_e,$flux,$type,$id_ce);
            return $id_fe;
        }

        
	public function doSupprimer(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$flux = $recuperateur->get('flux');
		$type = $recuperateur->get('type');
		$this->hasDroitEdition($id_e);
		$this->FluxEntiteSQL->deleteConnecteur($id_e,$flux,$type);
		$this->LastMessage->setLastMessage("L'association a été supprimée");
		$this->redirect("/entite/detail.php?id_e=$id_e&page=4");
	}
	
	

	public function editionAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->id_e = $recuperateur->getInt('id_e');
		$this->flux = $recuperateur->get('flux');
		$this->type_connecteur = $recuperateur->get('type');
		
		$this->connecteur_disponible = $this->FluxControler->getConnecteurDispo($this->id_e,$this->type_connecteur);
		
		$this->page_title = "Association d'un connecteur et d'un flux";
		$this->template_milieu = "FluxEdition";
		$this->renderDefault();
	}
	
	
}