<?php
class HorodateurPastell extends Horodateur {
	
	private $signerCertificate;
	private $signerKey;
	private $signerKeyPassword;
	private $ca_certificate;

	
	public function setConnecteurConfig(DonneesFormulaire $donnesFormulaire){
		$this->signerCertificate = $donnesFormulaire->getFilePath('signer_certificate');
		$this->signerKey = $donnesFormulaire->getFilePath('signer_key');
		$this->signerKeyPassword = $donnesFormulaire->get('signer_key_password');
		$this->ca_certificate = $donnesFormulaire->getFilePath("ca_certificate");
	}
	
	public function getTimestampReply($data){
		$timestampRequest = $this->opensslTSWrapper->getTimestampQuery($data);
		$config_file = __DIR__."/data/openssl-tsa.cnf";
		return $this->opensslTSWrapper->createTimestampReply($timestampRequest,$this->signerCertificate,$this->signerKey,$this->signerKeyPassword,$config_file);
	}	
	
	public function verify($data,$token){
		$result = $this->opensslTSWrapper->verify($data,$token,$this->ca_certificate,$this->signerCertificate);
		if (!$result){
			throw new Exception($this->opensslTSWrapper->getLastError());
		}
		return $result;
	}

}