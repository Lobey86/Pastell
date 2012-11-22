<?php

class CertificatConnexion {
	
	private $sqlQuery;
	private $certificat;
	
	public function __construct(SQLQuery $sqlQuery, $server = false){
		$this->sqlQuery = $sqlQuery;
		
		if ( ! $server){
			$server = $_SERVER;
		}
		
		$certificat_client = "";
		if (isset($server['SSL_CLIENT_CERT'])){
			$certificat_client = $server['SSL_CLIENT_CERT'];
		}
		$this->setCertificat(new Certificat($certificat_client));		
	}
	
	public function setCertificat(Certificat $certificat){
		$this->certificat = $certificat;
	}
	
	
	public function connexionGranted($id_u){
		
		$sql = "SELECT certificat_verif_number FROM utilisateur WHERE id_u=?";
		$certif_verif_number = $this->sqlQuery->fetchOneValue($sql,$id_u);
		
		if (! $certif_verif_number){
			return true;
		}
		
		return $certif_verif_number == $this->certificat->getVerifNumber();
	}
	
	public function autoConnect(){
		$verifNumber = $this->certificat->getVerifNumber();
		if (! $verifNumber){
			return false;
		}
		$sql = "SELECT id_u FROM utilisateur WHERE certificat_verif_number = ?" ;
		$all = $this->sqlQuery->fetchAll($sql,$verifNumber); 
		if (count($all) == 1){
			return $all[0]['id_u'];
		}
		return false;
	}
	
}