<?php 

class RoleControler extends PastellControler {
	
	public function indexAction(){
		$this->verifDroit(0,"role:lecture");
		$this->allRole = $this->RoleSQL->getAllRole();
		if ($this->hasDroit(0,"role:edition")){
			$this->nouveau_bouton_url = array("Nouveau" => "role/edition.php");
		} 
		$this->page_title = "Liste des rôles";
		$this->template_milieu = "RoleIndex";
		$this->renderDefault();
	}
	
	public function detailAction(){
		$this->verifDroit(0,"role:lecture");
		$recuperateur = new Recuperateur($_GET);
		$this->role = $recuperateur->get('role');
		$this->role_edition = $this->hasDroit(0,"role:edition");
		$this->role_info = $this->RoleSQL->getInfo($this->role);
		$all_droit = $this->RoleDroit->getAllDroit();
		$this->all_droit_utilisateur = $this->RoleSQL->getDroit($all_droit,$this->role);
		
		$this->page_title = "Droits associés au rôle {$this->role}";
		$this->template_milieu = "RoleDetail";
		$this->renderDefault();
	}
	
	public function editionAction(){
		$this->verifDroit(0,"role:edition");
		$recuperateur = new Recuperateur($_GET);
		$role = $recuperateur->get('role');
		
		if ($role){
			$this->page_title = "Modification du r&ocirc;le $role ";
			$this->role_info = $this->RoleSQL->getInfo($role);
		} else {
			$this->page_title = "Ajout d'un r&ocirc;le";	
			$this->role_info = array('libelle'=>'','role'=>'');
		}
		$this->template_milieu = "RoleEdition";
		$this->renderDefault();
	}
	
	public function doEditionAction(){
		$this->verifDroit(0,"role:edition");
		$recuperateur = new Recuperateur($_POST);
		$role = $recuperateur->get('role');
		$libelle = $recuperateur->get('libelle');
		$this->RoleSQL->edit($role,$libelle);
		$this->redirect("/role/detail.php?role=$role");
	}
	
	public function doDeleteAction(){
		$this->verifDroit(0,"role:edition");
		$recuperateur = new Recuperateur($_POST);
		$role = $recuperateur->get('role');
		
		if ($this->RoleUtilisateur->anybodyHasRole($role)){
			$this->LastError->setLastError("Le rôle $role est attribué à des utilisateurs");
			$this->redirect("/role/detail.php?role=$role");
		}
		
		$this->RoleSQL->delete($role);
		$this->LastMessage->setLastMessage("Le rôle $role a été supprimé");
		$this->redirect("/role/index.php");
	}
	
	public function doDetailAction(){
		$this->verifDroit(0,"role:edition");
		$recuperateur = new Recuperateur($_POST);
		$role = $recuperateur->get('role');
		$droit = $recuperateur->get('droit',array());
		$this->RoleSQL->updateDroit($role,$droit);
		$this->LastMessage->setLastMessage("Le rôle $role a été mis à jour");
		$this->redirect("/role/detail.php?role=$role");
	}
	
}