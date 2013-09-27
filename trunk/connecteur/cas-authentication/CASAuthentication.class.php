<?php 
require_once 'CAS.php';

class CASAuthentication extends Connecteur {
	
	private $host;
	private $port;
	private $context;
	private $ca_file;
	private $proxy;
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->host = $donneesFormulaire->get('cas_host');
		$this->port = $donneesFormulaire->get('cas_port');
		$this->context = $donneesFormulaire->get('cas_context');
		$this->ca_file = $donneesFormulaire->getFilePath('cas_ca');
		$this->proxy = $donneesFormulaire->getFilePath('cas_proxy');
		$cas_debug = $donneesFormulaire->getFilePath('cas_debug');
		if ($cas_debug) {
			phpCAS::setDebug($cas_debug);
		}
	}
	
	private function setClient(){
		phpCAS::client(CAS_VERSION_2_0, $this->host,$this->port,$this->context);
		phpCAS::setCasServerCACert($this->ca_file);
		if ($this->proxy){
			phpCAS::allowProxyChain(new CAS_ProxyChain(array($this->proxy)));
		}
	}
	
	public function isSessionAuthenticated(){
		$this->setClient();
		if (phpCAS::isSessionAuthenticated()){
			phpCAS::handleLogoutRequests(false);
			phpCAS::forceAuthentication();
			return phpCAS::getUser();
		}
		return false;
	}
	
	public function authenticate($url){
		$this->setClient();
		phpCAS::setFixedServiceURL($url);
		phpCAS::handleLogoutRequests(false);
		phpCAS::forceAuthentication();
		return phpCAS::getUser();
	}
	
	public function logout(){
		$this->setClient();
		return phpCAS::logout();
	}
	
	
}