<?php
require_once( PASTELL_PATH . "/lib/connecteur/megalis/Megalis.class.php");

class MegalisTestSSH extends ActionExecutor {
	
	public function go(){
		$megalis = new Megalis($this->getDonneesFormulaire(),new SSH2());
		$directory_listing = $megalis->listDirectory();
		if (! $directory_listing ){
			$this->setLastMessage($megalis->getLastError());
			return false;	
		}
		$this->setLastMessage("Connexion SSH OK. <br/>Contenu du répertoire : ".implode(", ",$directory_listing));
		return true;
	}
	
}