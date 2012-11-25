<?php
require_once( PASTELL_PATH . "/lib/helper/mail_validator.php");

class UtilisateurCreator extends SQL {
	
	private $passwordGenerator;
	private $journal;
	
	public function __construct(SQLQuery $sqlQuery,Journal $journal,PasswordGenerator $passwordGenerator){
		parent::__construct($sqlQuery);
		$this->passwordGenerator = $passwordGenerator;
		$this->journal = $journal;
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
		
		if (! is_mail($email)){
			$this->lastError ="Votre adresse email ne semble pas valide";
			return false;
		}

		if ($this->loginExists($login)){
			$this->lastError = "Ce login existe déjà";
			return false;
		}
		
		$password_validation = $this->passwordGenerator->getPassword();
		
		$sql = "INSERT INTO utilisateur(login,password,email,mail_verif_password,date_inscription) " . 
				" VALUES (?,?,?,?,now())";
		$this->query($sql,array($login,$password,$email,$password_validation));
		
		$id_u =  $this->queryOne("SELECT id_u FROM utilisateur WHERE login=?",array($login));
		
		
		return $id_u;
	}
	
	public function loginExists($login){
		$sql = "SELECT count(*) FROM utilisateur WHERE login = ?";
		return $this->queryOne($sql,array($login));
	}
	
}