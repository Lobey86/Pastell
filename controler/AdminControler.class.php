<?php
class AdminControler extends Controler {
	
	public function createAdmin($login,$password,$email){
		
		$this->fixDroit();
		$id_u = $this->UtilisateurCreator->create($login,$password,$password,$email);
		if (!$id_u){
			$this->lastError = $this->UtilisateurCreator->getLastError();
			return false; 
		}
		
		
		$this->Utilisateur->validMailAuto($id_u);
		$this->Utilisateur->setColBase($id_u,0);
		$this->RoleUtilisateur->addRole($id_u,"admin",0);
		
		
		
		
		return true;
	}
	
	public function fixDroit(){
	
		foreach($this->RoleDroit->getAllDroit() as $droit){
			$this->RoleSQL->addDroit("admin",$droit);
		}
		$this->EntiteCreator->updateAllEntiteAncetre();
	}
	
	
	
}

