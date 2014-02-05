<?php

class UtilisateurCreator {
	
	private $passwordGenerator;
	private $utilisateurSQL;
	private $lastError;
	
	public function __construct(PasswordGenerator $passwordGenerator, Utilisateur $utilisateurSQL){
		$this->passwordGenerator = $passwordGenerator;
		$this->utilisateurSQL = $utilisateurSQL;
	}

	public function getLastError(){
		return $this->lastError;
	}
	
	public function create($login,$password,$password2,$email){
		if ( ! $login ){
			$this->lastError = "Il faut saisir un login";
			return false;
		}

		if ( ! $password ){
			$this->lastError = "Il faut saisir un mot de passe";
			return false;
		}
	
		if ($password != $password2){
			$this->lastError = "Les mots de passes ne correspondent pas";
			return false;
		}
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$this->lastError ="Votre adresse email ne semble pas valide";
			return false;
		}

		if ($this->utilisateurSQL->getIdFromLogin($login)){
			$this->lastError = "Ce login existe déjà";
			return false;
		}
		
		$password_validation = $this->passwordGenerator->getPassword();
		return $this->utilisateurSQL->create($login,$password,$email,$password_validation);
	}
}