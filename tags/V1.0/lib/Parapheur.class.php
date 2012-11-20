<?php
/**
	Cette classe ne fonctionne qui si on a défini les trois constante suivantes (qui sont des noms de fichiers)  :
 	TIMESTAMPING_CERT => le fichier contenant le certificat au format PEM
	TIMESTAMPING_PRIV_KEY => la clé privée au format PEM protegé par un mot de passe
	TIMESTAMPING_PRIV_KEY_PASS => le fichier contenant le mot de passe
**/
/**
openssl smime -binary -sign -in gros  -inkey /etc/tedetis/ssl/tedetis_timestamp_priv_key.pem   -signer /etc/tedetis/ssl/tedetis_timestamp_cert.pem -passin pass:toto -binary -outform DER | openssl smime -pk7out -inform DER | openssl smime -inform PEM -verify -content gros -CAfile /etc/tedetis/ssl/tedetis_timestamp_cert.pem > /dev/null
*/

class Parapheur {
	
	private $data;
	private $last_error;
		
	private $certSigner;
	private $keySigner;
	private $keyPassword;
	
	public function __construct($data){
		$this->data = $data;
		$this->last_error = "";
		//Par défaut on construit avec les paramètre de timestamp
		$this->setSignerParameter(TIMESTAMPING_CERT,TIMESTAMPING_PRIV_KEY,null);
		$this->setKeyPaswordFromFile(TIMESTAMPING_PRIV_KEY_PASS);
	}
	
	public function setSignerParameter($certSigner,$keySigner,$keyPassword){
		$this->certSigner = $certSigner;
		$this->keySigner = $keySigner;
		$this->keyPassword = $keyPassword;
	}
	
	public function setKeyPaswordFromFile($passwordFile){
		$this->keyPassword = `sudo /bin/cat $passwordFile `;
	}
	
	public function getLastError(){
		return $this->last_error;
	}
	
	public function getSignature() {
		
		$inFileName = $this->writeDataToFile();
		$tmpFileName = tempnam("/tmp","signed_tmp_");
		
		$cert = $this->certSigner;
		$privateKey = $this->keySigner;
		$password = $this->keyPassword;
				
	
		$cmd = "openssl smime -binary -sign -in $inFileName  -inkey $privateKey -signer $cert -binary -outform DER -out $tmpFileName -passin pass:$password";

		Trace::wrap_exec($cmd,$out,$return);
		
		unlink($inFileName);
		
		if ($return != 0){		
			$this->last_error = "Erreur : " . implode("\n", $out);
			return false;
		} 
			
		$cmd = "openssl smime -pk7out -inform DER -in $tmpFileName ";
		Trace::wrap_exec($cmd,$out,$return);
		unlink($tmpFileName);
		
		if ($return != 0){		
			$this->last_error = "Erreur : " . implode("\n", $out);
			return false;
		} 
		
		$signature = implode("\n",$out);
		$signature.="\n";
			
		return $signature;
				
	}

	private function writeDataToFile(){
		$inFileName = tempnam('/tmp', 'paraph_');
		$inFile = fopen($inFileName,"w");
		fwrite($inFile,$this->data);
		fclose($inFile);
		return $inFileName;
	}
	
	//$signature est une chaine PKCS7 au format PEM
	public function verify($signature) {
		$signFileName = tempnam('/tmp', 'sign_');
		$signFile = fopen($signFileName,"w");
		fwrite($signFile,$signature);
		fclose($signFile);
		
		$dataFile = $this->writeDataToFile();
		
		$cert = $this->getCertificate($signFileName);
		
		if ( ! $cert ) {
	  		$this->last_error .= "\nErreur lors de l'extraction du certificat du PKCS7";
	  		return false;
		}
		
		if ($this->isCertificateRevoked($cert)){
			$this->last_error = "Le certificat utilisé pour la signature est révoqué";
			return false;
		}
		
		$cert = AUTHORIZED_SIGN_CA_PATH;
		$cmd ="openssl smime -in $signFileName -inform PEM -verify -content $dataFile -CApath $cert >/dev/null 2>&1";
		Trace::wrap_exec($cmd,$out,$return);
				
		unlink($dataFile);
		unlink($signFileName);
		
		if ($return != 0 ){
			$this->last_error =  implode("\n", $out);
			return false;
		}
		
		return true;
  	}
  	
	/**
	* \brief Méthode d'extraction d'un certificat x509 d'un fichier PKCS7
	* \param $signFile chaîne : Chemin vers le fichier PKCS7
	* \return Une chaîne contenant le certificat encodé en base64 ou false en cas d'erreur
	*/
	public function getCertificate($signatureFileName){
	
		$extractCmd = "openssl pkcs7 -in " . $signatureFileName . " -print_certs | openssl x509";
	
		
		Trace::wrap_exec($extractCmd, $out,$ret);

		if ( $ret ) {
		  $this->last_error = "Erreur d'extraction du certificat";
		  return false;
		}
		
		$cert = implode("\n",$out);
		$cert.="\n";
		
		return $cert;
	  }
  
	 /**
   * \brief Méthode de vérification de la validité d'un certificat (incluant les crl)
   * \param $cert chaîne : Certificat en base64 à contrôler
   * \return True si le certificat est révoqué, false sinon
   */
  public function isCertificateRevoked($cert) {
	$certFile = tempnam('/tmp', 'tedetis_sign_cert_');
	if (! file_put_contents($certFile, $cert)) {
	  $this->last_error = "Erreur système de fichiers";
	  return -1;
	}

	$verifyCmd = "openssl verify -CApath " . AUTHORIZED_SIGN_CA_PATH . " -crl_check " . $certFile;

	Trace::wrap_exec($verifyCmd, $out, $ret);

	unlink($certFile);

	$revoked = false;

	if ($ret != 0) {
	  self::$last_error = "Erreur de vérification des CRL";
	  return false;
	} else {
	  foreach ($out as $line) {
		if (stripos($line, 'certificate revoked') !== false) {
		  $revoked = true;
		} elseif (stripos($line, 'unable to get certificate CRL') !== false) {
		  // rien
		}
	  }
	}

	return $revoked;
  }
	
	
}