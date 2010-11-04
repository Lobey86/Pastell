<?php


class Authentification {
	
	private $session;
	
	public function __construct( & $_session = null){
		if (! $_session){
			$_session = & $_SESSION;
		}
		$this->session = & $_session;
	}
	
	public function connexion($login,$id_u){
		$this->session['connexion']['login'] = $login;
		$this->session['connexion']['id_u'] = $id_u;
		$this->session['connexion']['breadcrumbs'] = array();
	}
	
	public function isConnected(){
		return isset($this->session['connexion']);
	}
	
	public function getLogin(){
		assert('$this->isConnected()');
		return $this->session['connexion']['login'];
	}
	
	public function getId(){
		assert('$this->isConnected()');
		return $this->session['connexion']['id_u'];
	}
	
	
	public function deconnexion(){
		unset($this->session['connexion']);
	}

	public function setBreadCrumbs($bc){
		$this->session['connexion']['breadcrumbs'] = $bc;
	}
	
	public function getBreadCrumbs(){
		if (empty($this->session['connexion']['breadcrumbs'])){
			return false;
		}
		return $this->session['connexion']['breadcrumbs'];
	}
	
	
}