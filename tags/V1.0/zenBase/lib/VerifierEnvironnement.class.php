<?php 
class VerifierEnvironnement {
	
	private $php_version;
	
	public function __construct(){
		$this->setCurrentPHPVersion(PHP_VERSION) ;
	}	
	
	public function setCurrentPHPVersion($version){
		$this->php_version = $version;
	}
	
	public function isPHPVersionOK($min_php_version){		
		return version_compare($min_php_version,$this->php_version,"<=");
	}	
}