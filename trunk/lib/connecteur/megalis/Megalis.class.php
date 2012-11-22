<?php
require_once( PASTELL_PATH . "/lib/connecteur/Connecteur.class.php");

class Megalis extends Connecteur {
	
	private $collectiviteProperties;
	private $ssh2;
	
	public function __construct(DonneesFormulaire $collectiviteProperties, SSH2 $ssh2){
		$this->collectiviteProperties = $collectiviteProperties;
		$this->ssh2 = $ssh2;
	}
	
	private function getProperties($name){
		return $this->collectiviteProperties->get($name);
	}
	
	public function listDirectory(){
		$this->ssh2->setServerName($this->getProperties('emegalis_ssh_server'),$this->getProperties('emegalis_ssh_fingerprint'));
		$this->ssh2->setPasswordAuthentication($this->getProperties('emegalis_ssh_login'),$this->getProperties('emegalis_ssh_password'));
		$directory_listing = $this->ssh2->listDirectory($this->getProperties("emegalis_ssh_directory"));
		if (!$directory_listing){
			$this->lastError = $this->ssh2->getLastError();
			return false;
		}
		return $directory_listing;
	}
	
	public function recup(){
		$directory_listing = $this->listDirectory();
		if (!$directory_listing){
			return false;
		}
		
		$file_ok = array();
		$file_ignored = array(); 
		
		foreach($directory_listing as $file_name){
			if (in_array($file_name,array('.','..'))){
				continue;
			}
			if ($this->isValidEmpreinteFileName($file_name)){
				continue;
			}
			if (! $this->isValidFileName($file_name)){
				$file_ignored[] = $file_name;
				continue;
			}
			$file_ok[] = $file_name;
		}
		return array('file_ok' => $file_ok,'file_ignored' => $file_ignored);
	}
	
	public function retrieveFile($file_name,$destination){
		if (! $this->ssh2->retrieveFile($this->getProperties("emegalis_ssh_directory") . "/" . $file_name,$destination)){
			$this->lastError = $this->ssh2->getLastError();
			return false;
		}
		$md5_content = $this->ssh2->getFileContent($this->getProperties("emegalis_ssh_directory") . "/" .$file_name.".md5");
		if (!$md5_content){
			$this->lastError = "Impossible de trouver le fichier d'empreinte $file_name.md5";
			return false;
		}
		$md5_calcule = md5_file($destination);
		if ($md5_calcule != $md5_content){
			$this->lastError = "L'empreinte calculé du fichier ($md5_calcule) diffère de l'empreinte reçu ($md5_content)";
			return false; 
		}
		
		return $md5_content;
	}
	
	
	public function getSiren($file_name){
		return substr($file_name,0,9);
	}
	
	
	private function isValidEmpreinteFileName($file_name){
		return preg_match("#^\d{9}.*\.zip.md5#",$file_name);
	}
	
	private function isValidFileName($file_name){
		return preg_match("#^\d{9}.*\.zip#",$file_name);
	}
	
}