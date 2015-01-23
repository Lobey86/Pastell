<?php
class RecuperationFichierSSHTest extends ActionExecutor {
	
	public function go(){
		$recuperationFichierSSH = $this->getMyConnecteur();
		$directory_listing = $recuperationFichierSSH->listFile();
		$this->setLastMessage("Connexion SSH OK. <br/>Contenu du répertoire : ".implode(", ",$directory_listing));
		return true;
	}
	
}