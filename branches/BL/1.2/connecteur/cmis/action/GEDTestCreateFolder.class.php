<?php

class GEDTestCreateFolder extends ActionExecutor {

	public function go(){
		$cmis = $this->getMyConnecteur();
		$rootFolder = $cmis->getRootFolder();
				
		$folderName = $this->objectInstancier->PasswordGenerator->getPassword();
		$reponseCMIS = $cmis->createFolder($rootFolder,$folderName,"Pastell - Création d'un répertoire de test");
		if (! $reponseCMIS){
			$this->setLastMessage("La création du répertoire $folderName a échoué : " . $cmis->getLastError());
			return false;
		}
		$this->setLastMessage("Création du répertoire $folderName : $reponseCMIS ");
		return true;
	}

}