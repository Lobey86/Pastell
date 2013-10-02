<?php 
require_once 'CAS.php';

class CASAuthentication extends Connecteur {
	
	private $host;
	private $port;
	private $context;
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->host = $donneesFormulaire->get('cas_host');
		$this->port = $donneesFormulaire->get('cas_port');
		$this->context = $donneesFormulaire->get('cas_context');
		$this->ca_file = $donneesFormulaire->getFilePath('cas_ca');
		phpCAS::setDebug("/tmp/cas.log");
	}
	
	public function isSessionAuthenticated(){
		phpCAS::client(CAS_VERSION_2_0, $this->host,$this->port,$this->context);
		phpCAS::setCasServerCACert($this->ca_file);
		if (phpCAS::isSessionAuthenticated()){
			phpCAS::handleLogoutRequests(false);
			phpCAS::forceAuthentication();
			return phpCAS::getUser();
		}
		return false;
	}
	
	public function authenticate($url){
		phpCAS::client(CAS_VERSION_2_0, $this->host,$this->port,$this->context);
		phpCAS::setCasServerCACert($this->ca_file);
		phpCAS::setFixedServiceURL($url);
		phpCAS::handleLogoutRequests(false);
		phpCAS::forceAuthentication();
		return phpCAS::getUser();
	}
	
	public function logout(){
		phpCAS::client(CAS_VERSION_2_0, $this->host,$this->port,$this->context);
		phpCAS::setCasServerCACert($this->ca_file);
		return phpCAS::logout();
	}
	
	
}