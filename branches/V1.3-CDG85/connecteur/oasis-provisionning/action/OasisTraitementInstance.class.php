<?php

class OasisTraitementInstance extends ActionExecutor {
	
	public function go(){
		$oasisProvisionning = $this->getMyConnecteur();
		
		$instance_info = $oasisProvisionning->getNextInstance();
		
		$id_u = $this->objectInstancier->Utilisateur->getIdFromLogin($instance_info['user_id']);
		
		if (! $id_u){
			$password = $this->objectInstancier->PasswordGenerator->getPassword();
			$id_u = $this->objectInstancier->UtilisateurCreator->create($instance_info['user_id'],$password,$password,$instance_info['user']['email_address']);
			$this->objectInstancier->Utilisateur->setNomPrenom($id_u,$instance_info['user']['email_address'],"");
		}
		
		$id_e = $this->objectInstancier->EntiteCreator->edit(0,"",$instance_info['organization_name'],Entite::TYPE_COLLECTIVITE,0,0);
		
		$oasisProvisionningConfig = $this->getConnecteurConfig($this->id_ce);
		
		$role = $oasisProvisionningConfig->get('role');
		
		$this->objectInstancier->RoleUtilisateur->addRole($id_u,$role,$id_e);
		
		
		$id_ce = $this->objectInstancier->ConnecteurEntiteSQL->addConnecteur($id_e,'openid-authentication','openid-authentication',"Authentification OpenID");
				
		$connecteurConfig = $this->objectInstancier->ConnecteurFactory->getConnecteurConfig($id_ce);
		
		$connecteurConfig->setData('client_id',$instance_info['client_id']);
		$connecteurConfig->setData('client_secret',$instance_info['client_secret']);
		$connecteurConfig->setData('instance_id',$instance_info['instance_id']);

		$this->objectInstancier->FluxEntiteSQL->addConnecteur($id_e,'openid-authentification','openid-authentication',$id_ce);
						
		$oasisProvisionning->aknowledge($instance_info,$id_e);
		
		$oasisProvisionning->deleteNextInstance();
		$this->setLastMessage("L'instance {$instance_info['organization_name']} a été créée avec succès" );
		return true;
	}
	
}