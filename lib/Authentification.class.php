<?php
class Authentification {
	
	public function connexion($login,$id_u){
		$_SESSION['connexion']['login'] = $login;
		$_SESSION['connexion']['id_u'] = $id_u;
		$_SESSION['connexion']['breadcrumbs'] = array();
		
	}
	
	public function isConnected(){
		return isset($_SESSION['connexion']);
	}
	
	public function getLogin(){		
		return $_SESSION['connexion']['login'];
	}
	
	public function getId(){
		return $_SESSION['connexion']['id_u'];
	}
	
	public function deconnexion(){
		unset($_SESSION['connexion']);
	}
}