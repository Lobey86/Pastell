<?php
class FileUploader {
	
	private $files;
	private $lastError;
	
	public function __construct(){
		$this->setFiles($_FILES);
	}
	
	public function setFiles($files){
		$this->files = $files;
	}
	
	public function getFilePath($filename){
		return $this->getValue($filename,'tmp_name');
	}
	
	public function getName($filename){
		return $this->getValue($filename,'name');
	}
	
	public function getLastError(){
		switch($this->lastError){
			case UPLOAD_ERR_INI_SIZE: return "Le fichier dépasse ". ini_get("upload_max_filesize");
			case UPLOAD_ERR_FORM_SIZE : return "Le fichier dépasse la taille limite autorisé par le formulaire";
			case UPLOAD_ERR_PARTIAL: return "Le fichier n'a été que partiellement reçu";
			case UPLOAD_ERR_NO_FILE: return "Aucun fichier n'a été reçu";
			case UPLOAD_ERR_NO_TMP_DIR: return "Erreur de configuration : le répertoire temporaire n'existe pas";
			case UPLOAD_ERR_CANT_WRITE  : return "Erreur de configuration : Impossible d'écrire dans le répertoire temporaire";
			case UPLOAD_ERR_EXTENSION  : return "Une extension PHP empeche l'upload du fichier!";
			default: return "Aucun fichier reçu (code : {$this->lastError})";	
		}
	}
	
	private function getValue($filename,$value){
		if (! isset($this->files[$filename]['error'])){
			return false;
		}
		if ($this->files[$filename]['error'] != UPLOAD_ERR_OK){
			$this->lastError = $this->files[$filename]['error'];
			return false;
		}
		
		if (empty($this->files[$filename][$value])){
			return false;
		}
		return $this->files[$filename][$value];
	}
	
	public function getFileContent($form_name){
		if (empty($_FILES[$form_name]['tmp_name'])){
			return false;
		}
		return file_get_contents($_FILES[$form_name]['tmp_name']);
	}
	
	public function save($filename,$save_path){
		move_uploaded_file($this->getFilePath($filename),$save_path);
	}
	
	public function getAll(){
		$i = 1;
		$result = array();
		foreach($this->files as $filename => $value){
			$result[$filename] = $this->getName($filename);
		}
		return $result;
	}
}	