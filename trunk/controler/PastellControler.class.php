<?php
class PastellControler extends Controler {
	

	public function hasDroitEdition($id_e){
		$droit_ecriture = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),
															"entite:edition",$id_e);
		if ( ! $droit_ecriture ){
			$this->LastError->setLastError("Vous n'avez pas le droit d'édition sur cette entité");
			$this->redirect("/entite/detail.php?id_e=$id_e&page=2");
		}

		if ( $id_e && ! $this->EntiteSQL->getInfo($id_e)){
			$this->LastError->setLastError("L'entité $id_e n'existe pas");
			$this->redirect("/index.php");
		}
	}
	
	public function hasDroitLecture($id_e){
		$droit_lecture = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),
															"entite:lecture",$id_e);
		if ( ! $droit_lecture ){
			$this->LastError->setLastError("Vous n'avez pas le droit de lecture sur cette entité");
			$this->redirect("/entite/detail.php?id_e=$id_e");
		}

		if ( $id_e && ! $this->EntiteSQL->getInfo($id_e)){
			$this->LastError->setLastError("L'entité $id_e n'existe pas");
			$this->redirect("/index.php");
		}
	}
	
	public function ensureDroit($droit,$id_e,$redirect_to = ""){
		if  ($this->RoleUtilisateur->hasDroit($this->Authentification->getId(),$droit,$id_e)){
			return true;
		}
		$this->LastError->setLastError("Vous n'avez pas les droits nécessaire pour accéder à cette page");
		$this->redirect($redirect_to);
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