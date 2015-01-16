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
		return $this->certData['name'];
	}
	
	//http://stackoverflow.com/questions/6426438/php-ssl-certificate-serial-number-in-hexadecimal
	public function getSerialNumber(){
		$base = bcpow("2", "32");
		$counter = 100;
		$res = "";
        $val = $this->certData['serialNumber'];
		while($counter > 0 && $val > 0) {
			$counter = $counter - 1;
			$tmpres = dechex(bcmod($val, $base)) . "";
			for ($i = 8-strlen($tmpres); $i > 0; $i = $i-1) {
				$tmpres = "0$tmpres";
			}
			$res = $tmpres .$res;
			$val = bcdiv($val, $base);
		}
		if ($counter <= 0) {
			return false;
		}
		return strtoupper($res);
	}
	
	public function getIssuer(){
		$data = array();
		foreach($this->certData['issuer'] as $name => $value){
			$data[] = "$name=$value";
		}
		
		return "/".implode('/',$data);
	}
	
}