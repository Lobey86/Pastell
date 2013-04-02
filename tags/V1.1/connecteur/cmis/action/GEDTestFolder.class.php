<?php

class GEDTestFolder extends ActionExecutor {
	
	public function go(){
				
		$cmis = $this->getMyConnecteur();
		
		$info = $cmis->testObject();
		
		if (! $info){
			$this->setLastMessage("La connexion avec la GED a échoué : " . $cmis->getLastError());
			return false;
		}

		$message ="La connexion est réussi - Pastell a récupéré les informations suivantes :<ul>" ;
		
		foreach($cmis->getFolderRetrieveInfo() as $repoInfo){
					$message .= "<li> $repoInfo  : ".$info[$repoInfo] ."</li>";
		}
		$message .="</ul>";
		$this->setLastMessage($message);
		return true;
	}
	
}