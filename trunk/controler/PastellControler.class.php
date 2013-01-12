<?php
class PastellControler extends Controler {
	
	public function hasDroit($id_e,$droit){
		return $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),$droit,$id_e);
	}
	
	public function verifDroit($id_e,$droit,$redirect_to = ""){
		if ( $id_e && ! $this->EntiteSQL->getInfo($id_e)){
			$this->LastError->setLastError("L'entité $id_e n'existe pas");
			$this->redirect("/index.php");
		}
		if  ($this->hasDroit($id_e,$droit)){
			return true;
		}
		$this->LastError->setLastError("Vous n'avez pas les droits nécessaire pour accéder à cette page");
		$this->redirect($redirect_to);
	}
	
	public function hasDroitEdition($id_e){
		$this->verifDroit($id_e,"entite:edition");
	}
	
	public function hasDroitLecture($id_e){
		$this->verifDroit($id_e,"entite:lecture");
	}
	
	public function renderDefault(){
		$this->authentification = $this->Authentification;
		$this->roleUtilisateur = $this->RoleUtilisateur;
		$this->sqlQuery = $this->SQLQuery;
		$this->objectInstancier = $this->ObjectInstancier;
		$this->versionning = $this->Versionning;
		$this->timer = $this->Timer;
		parent::renderDefault();
	}
	
}