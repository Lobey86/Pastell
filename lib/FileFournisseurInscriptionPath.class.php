<?php


class FileFournisseurInscriptionPath {
	
	private $directoryPath;
	
	public function __construct($directoryPath){
		$this->directoryPath = $directoryPath;
	}
	
	public function getFilePath($siren){
		return $this->directoryPath . "/" . $siren . ".yml";
	}
	
}