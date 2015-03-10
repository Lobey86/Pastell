<?php
class RecuperationFichierLocalTest extends ActionExecutor {
	
	public function go(){
		$recuperationFichierLocal = $this->getMyConnecteur();
		$directory_listing = $recuperationFichierLocal->listFile();
		$this->setLastMessage("Lecture ok <br/>Contenu du répertoire : ".implode(", ",$directory_listing));
		return true;
	}
	
}