<?php
class AsalaeREST extends SAEConnecteur {
	
	private $curlWrapper;
	private $tmpFile;
	
	private $url;
	private $login;
	private $password;
	private $originatingAgency;
	
	private $last_error_code;
	
	private $connecteur_config;
	
	public function __construct(CurlWrapper $curlWrapper, TmpFile $tmpFile){
		$this->curlWrapper = $curlWrapper;
		$this->tmpFile = $tmpFile; 
	}
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire) {
		$this->url = $donneesFormulaire->get('url');
		$this->login = $donneesFormulaire->get('login');
		$this->password = $donneesFormulaire->get('password');
		$this->originatingAgency = $donneesFormulaire->get('originating_agency');
		$this->connecteur_config = $donneesFormulaire;
	}
	
	public function sendArchive($bordereauSEDA,$archivePath,$file_type="TARGZ",$archive_file_name="archive.tar.gz") {
		$bordereau_file = $this->tmpFile->create();	
		file_put_contents($bordereau_file, $bordereauSEDA);
		
		$this->connecteur_config->addFileFromData('last_bordereau', 'bordereau_seda.xml', $bordereauSEDA);
		$this->connecteur_config->addFileFromData('last_file', 'donnes.tar.gz', file_get_contents($archivePath));
		
		$this->curlWrapper->addPostFile('seda_message', $bordereau_file,"bordereau.xml");
		$this->curlWrapper->addPostFile('attachments', $archivePath,$archive_file_name);		
		$result = $this->getWS('/sedaMessages');
		$this->tmpFile->delete($bordereau_file);
		return true;
	}
	
	public function getLastErrorCode(){
		return $this->last_error_code;
	}
	
	public function getErrorString($number){
		return "Erreur non identifié";
	}
	
	public function getAcuseReception($id_transfert) {
		$org = $this->originatingAgency;
		$result = $this->getWS("/sedaMessages/sequence:ArchiveTransfer/message:Acknowledgement/originOrganizationIdentification:$org/originMessageIdentifier:$id_transfert","application/xml");
		if (!$result){
			$this->last_error_code = 7;
			return false;
		}
		return $result;
	}
	
	public function getReply($id_transfert) {
		$org = $this->originatingAgency;
		$result = $this->getWS("/sedaMessages/sequence:ArchiveTransfer/message:ArchiveTransferReply/originOrganizationIdentification:$org/originMessageIdentifier:$id_transfert","application/xml");
		if (!$result){
			$this->last_error_code = 8;
			return false;
		}
		return $result;	
	}
	
	public function getURL($cote) {
		$tab = parse_url($this->url);
		return "{$tab['scheme']}://{$tab['host']}/archives/viewByArchiveIdentifier/$cote";
	}
	
	private function getWS($url,$accept = "application/json"){
		$this->curlWrapper->httpAuthentication($this->login, hash("sha256",$this->password));
		$this->curlWrapper->setAccept($accept);
		$this->curlWrapper->dontVerifySSLCACert();
		$result = $this->curlWrapper->get($this->url.$url);
		if (! $result){
			throw new Exception($this->curlWrapper->getLastError());
		}
		$http_code = $this->curlWrapper->getHTTPCode();
		if ($http_code != 200){
			$result = utf8_decode($result);
			throw new Exception("$result - code d'erreur HTTP : $http_code");
		}
		if ($accept == "application/json"){
			$result = utf8_decode($result);
			$result = json_decode($result,true); 
		}
		return $result;
	}
	
	public function getVersion(){
		return $this->getWS('/versions');
	}
	
	public function ping(){
		return $this->getWS('/ping');
	}
	
}