<?php
require_once(__DIR__."/../../connecteur-type/Horodateur.class.php");

class HorodateurPastell extends Horodateur {
	
	private $signerCertificate;
	private $signerKey;
	private $signerKeyPassword;

	
	public function setConnecteurConfig(DonneesFormulaire $donnesFormulaire){
		$this->signerCertificate = $donnesFormulaire->getFilePath('signer_certificate');
		$this->signerKey = $donnesFormulaire->getFilePath('signer_key');
		$this->signerKeyPassword = $donnesFormulaire->get('signer_key_password');
	}
	
	public function getTimestampReply($data){
		$timestampRequest = $this->opensslTSWrapper->getTimestampQuery($data);
		$config_file = __DIR__."/data/openssl-tsa.cnf";
		return $this->opensslTSWrapper->createTimestampReply($timestampRequest,$this->signerCertificate,$this->signerKey,$this->signerKeyPassword,$config_file);
	}	

}