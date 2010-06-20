<?php

class SSLManager {
	
	const SSL_CLIENT_VERIFY_SUCCESS = "SUCCESS";
	
	private $hasCertificate ;
	private $certificate;
	
	public function __construct($_server = null){
		if ( ! $_server){
			$_server = $_SERVER;
		}
		if (isset($_server['SSL_CLIENT_VERIFY'])  && $_server['SSL_CLIENT_VERIFY'] == self::SSL_CLIENT_VERIFY_SUCCESS)	{
			$this->hasCertificate = true;
			$this->certificate = array(
											'SSL_CLIENT_S_DN' => $_server['SSL_CLIENT_S_DN'],
											'SSL_CLIENT_I_DN' => $_server['SSL_CLIENT_I_DN']
										);
		}
	}
	
	public function hasCertificate(){
		return $this->hasCertificate;
	}
	
	public function getCertificate(){
		return serialize($this->certificate);
	}
	
	public function getCertificateCN(){
		$subject = $this->certificate['SSL_CLIENT_S_DN'];
		preg_match('/CN=([^:]*)/',$subject,$matches);
		return $matches[1];
	}
	
}

