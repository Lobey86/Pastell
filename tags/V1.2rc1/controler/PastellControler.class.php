<?php
class PastellControler extends Controler {
	
	public function getId_u(){
		return $this->Authentification->getId();
	}
	
	public function hasDroit($id_e,$droit){
		return $this->RoleUtilisateur->hasDroit($this->getId_u(),$droit,$id_e);
	}
	
	public function verifDroit($id_e,$droit,$redirect_to = ""){
		if ( $id_e && ! $this->EntiteSQL->getInfo($id_e)){
			$this->LastError->setLastError("L'entité $id_e n'existe pas");
			$this->redirect("/index.php");
		}
		if  ($this->hasDroit($id_e,$droit)){
			return true;
		}
		$this->LastError->setLastError("Vous n'avez pas les droits nécessaires pour accéder à cette page");
		$this->redirect($redirect_to);
	}
	
	public function hasDroitEdition($id_e){
		$this->verifDroit($id_e,"entite:edition");
	}
	
	public function hasDroitLecture($id_e){
		$this->verifDroit($id_e,"entite:lecture");
	}
	
	public function setNavigationInfo($id_e,$url){
		$listeCollectivite = $this->RoleUtilisateur->getEntite($this->getId_u(),"entite:lecture");
		$this->navigation_denomination = $this->EntiteSQL->getDenomination($id_e);
		$this->navigation_all_ancetre = $this->EntiteSQL->getAncetreNav($id_e,$listeCollectivite);
		$this->navigation_liste_fille = $this->EntiteSQL->getFilleInfoNavigation($id_e, $listeCollectivite);
		$this->navigation_entite_affiche_toutes = ($id_e != 0 && (count($listeCollectivite) > 1 || $listeCollectivite[0] == 0));
		$this->navigation_url = $url;
	}
		
		
	public function getAllModule(){
		$all_module = array();
		
		$allDocType = $this->DocumentTypeFactory->getAllType();
		$allDroit = $this->RoleUtilisateur->getAllDroit($this->Authentification->getId());
		
		foreach($allDocType as $type_flux => $les_flux){
			foreach($les_flux as $nom => $affichage) {
				if ($this->RoleUtilisateur->hasOneDroit($this->Authentification->getId(),$nom.":lecture")){
					$all_module[$type_flux][$nom]  = $affichage;
				}
			}
		}
		return $all_module;
	}
	
	public function renderDefault(){
		$this->all_module = $this->getAllModule();
		$this->authentification = $this->Authentification;
		$this->roleUtilisateur = $this->RoleUtilisateur;
		$this->sqlQuery = $this->SQLQuery;
		$this->objectInstancier = $this->ObjectInstancier;
		$this->versionning = $this->Versionning;
		$this->timer = $this->Timer;
		parent::renderDefault();
	}
	
}