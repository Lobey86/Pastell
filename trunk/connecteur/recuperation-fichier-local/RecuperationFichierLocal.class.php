<?php
class RecuperationFichierLocal extends RecuperationFichier {
	
	private $directory;
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->directory = $donneesFormulaire->get("directory");
	}
	
	public function listFile() {
		return scandir($this->directory);
	}
	
	public function retrieveFile($filename, $destination_directory){
		copy($this->directory."/$filename", $destination_directory."/".$filename);
	}
	
	public function deleteFile($filename){
		unlink($this->directory."/$filename");
	}
	
}