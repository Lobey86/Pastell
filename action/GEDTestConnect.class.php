<?php

require_once( PASTELL_PATH . "/lib/system/CMIS.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class GEDTestConnect extends ActionExecutor {
	
	public function go(){
				
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$activate = $donneesFormulaire->get('ged_activate');
		
		if (! $activate){
			$this->setLastMessage("La connexion avec la GED a échoué : le module n'est pas activé");
			return false;
		}
		
		$url = $donneesFormulaire->get('ged_url');
		$login = $donneesFormulaire->get('ged_user_login');
		$password = $donneesFormulaire->get('ged_user_password');
		
		$cmis = new CMIS($url,$login,$password);
		$info = $cmis->getRepositoryInfo();
		
		if (! $info){
			$this->setLastMessage("La connexion avec la GED a échoué : " . $cmis->getLastError());
			return false;
		}

		$message ="La connexion est réussi - Pastell a récupéré les informations suivantes :<ul>" ;
		
		foreach($cmis->getRepositoryRetrieveInfo() as $repoInfo){
					$message .= "<li> $repoInfo  : ".$info[$repoInfo] ."</li>";
		}
		$message .="</ul>";
		$this->setLastMessage($message);
		return true;
	}
	
}