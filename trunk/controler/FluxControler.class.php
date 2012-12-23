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
	
	public function hasGoodType($id_ce,$type){
		$info = $this->ConnecteurEntiteSQL->getInfo($id_ce);
		if ($info['type'] != $type){
			$this->LastError->setLastError("Le connecteur n'est pas du bon type");
			$this->redirect("/entite/detail.php?id_e=$id_e&page=3");
		}
	}
	
	public function doEditionModif(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$flux = $recuperateur->get('flux');
		$type = $recuperateur->get('type');
		$id_ce = $recuperateur->get('id_ce');

		$this->hasDroitEdition($id_e);
		$this->hasGoodType($id_ce, $type);
		
		$this->FluxEntiteSQL->addConnecteur($id_e,$flux,$type,$id_ce);
		
		$this->LastMessage->setLastMessage("Connecteur associé au flux avec succès");
		$this->redirect("/entite/detail.php?id_e=$id_e&page=4");
	}
	
	public function listFlux(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$this->hasDroitLecture($id_e);
		$this->id_e = $id_e;
		$this->all_flux_entite = $this->FluxEntiteSQL->getAll($id_e);
		
		
		if ($id_e){
			$this->all_flux = $this->FluxDefinitionFiles->getAll();
			$this->render("FluxList");
		} else {
			$this->all_flux = $this->ConnecteurDefinitionFiles->getAllGlobal();
			$this->all_flux_global = $this->all_flux_entite['global'];
			$this->render("FluxGlobalList");
		}
		
		
		
	}
	
	
}