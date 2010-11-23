<?php 

class Certificat {
	
	private $certificatPEM;
	private $certData;
	
	public function __construct($certificatPEM){
		$this->certificatPEM = $certificatPEM;
		$this->certData = openssl_x509_parse( $this->certificatPEM);
	}
	
	public function isValid(){
		return  ! empty($this->certData['name']);
	}
	
	public function getContent(){
		return $this->certificatPEM;
	}
	
	public function getInfo(){
		return $this->certData;
	}
	
	public function getVerifNumber(){
		if (! $this->isValid()){
			return false;
		}
		$chaine = "subject:";
		foreach($this->certData['subject'] as $name => $value){
			$chaine .= "$name=$value/";
		}
		$chaine.=";issuer=";
		foreach($this->certData['issuer'] as $name => $value){
			$chaine .= "$name=$value/";
		}
		
		return md5($chaine);	
	}
	
	public function getFancy(){
		if ( ! $this->isValid()){
			return false;
		}
		return $this->certData['subject']['CN'] . " (signé par ".$this->certData['issuer']['CN'].")" ;
	}
		
	
}