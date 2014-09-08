<?php
class AdminControler extends Controler {	
	public function createAdmin($login,$password,$email){
		$this->fixDroit();
		$id_u = $this->UtilisateurCreator->create($login,$password,$password,$email);
		if (!$id_u){
			$this->lastError = $this->UtilisateurCreator->getLastError();
			return false; 
		}
		//Ajout de l'affectation du nom (reprise du login) pour avoir accès à la fiche de l'utilisateur depuis l'IHM
		$this->Utilisateur->setNomPrenom($id_u,$login,"");             
		$this->Utilisateur->validMailAuto($id_u);
		$this->Utilisateur->setColBase($id_u,0);
		$this->RoleUtilisateur->addRole($id_u,"admin",0);
		return true;
	}
	
	public function fixDroit(){
		$this->RoleSQL->edit("admin","Administrateur");
		
		foreach($this->RoleDroit->getAllDroit() as $droit){
			$this->RoleSQL->addDroit("admin",$droit);
		}
		$this->EntiteCreator->updateAllEntiteAncetre();
	}
}

