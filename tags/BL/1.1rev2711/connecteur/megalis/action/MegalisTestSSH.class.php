<?php
class MegalisTestSSH extends ActionExecutor {
	
	public function go(){
		$megalis = $this->getMyConnecteur();
		$directory_listing = $megalis->listDirectory();
		if (! $directory_listing ){
			$this->setLastMessage($megalis->getLastError());
			return false;	
		}
		$this->setLastMessage("Connexion SSH OK. <br/>Contenu du répertoire : ".implode(", ",$directory_listing));
		return true;
	}
	
}