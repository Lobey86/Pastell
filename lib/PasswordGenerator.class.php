<?php 
class PasswordGenerator {
	
	const NB_SIGNE_DEFAULT = 7;
	
	const SIGNE = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
	private $nbSigne;
	private $signe;
	private $lengthSigne;
	
	public function __construct(){
		$this->nbSigne = self::NB_SIGNE_DEFAULT;	
		$this->setSigne(self::SIGNE);
	}
	
	public function setSigne($signe){
		$this->signe = $signe;
		$this->lengthSigne = strlen($this->signe);	
	}
	
	public function getPassword(){
		$password = "";
		for($i=0; $i<$this->nbSigne; $i++){
			$password.= $this->getLettre();
		}
		return $password;
	}
	
	private function getLettre(){
		return $this->signe[rand(0,$this->lengthSigne - 1)];		
	}	
}