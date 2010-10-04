<?php
require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");

class Utilisateur {

	const CREATE_SQL = "CREATE TABLE utilisateur (
  id_u int(11) NOT NULL AUTO_INCREMENT,
  email varchar(128) NOT NULL,
  login varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  mail_verif_password varchar(16) NOT NULL,
  date_inscription datetime NOT NULL,
  mail_verifie tinyint(1) NOT NULL,
  nom varchar(128) NOT NULL,
  prenom varchar(128) NOT NULL,
  PRIMARY KEY (id_u)
)";
	
	private $sqlQuery;
	private $id_u;
		
	public function __construct(SQLQuery $sqlQuery,$id_u){
		$this->sqlQuery = $sqlQuery;
		$this->id_u = $id_u;
	}
	
	public function setNomPrenom($nom,$prenom){
		$sql = "UPDATE utilisateur SET nom = ? , prenom = ? WHERE id_u = ?";
		$this->sqlQuery->query($sql,array($nom,$prenom,$this->id_u));
	}
	
	public function getInfo(){
		$sql = "SELECT * FROM utilisateur WHERE id_u = ?";
		return $this->sqlQuery->fetchOneLine($sql,array($this->id_u));
	}
	
	public function validMail($password){
		$sql = "SELECT id_u FROM utilisateur " . 
				" WHERE id_u =? AND mail_verif_password= ? ";
		$result = $this->sqlQuery->fetchOneValue($sql,array($this->id_u, $password));
		if ( ! $result){
			return false;
		}
		$this->validMailAuto();
		return true;
	}
	
	public function validMailAuto(){
		$sql = "UPDATE utilisateur SET mail_verifie=1 WHERE id_u=?";
		$this->sqlQuery->query($sql, array($this->id_u));
	}
	
	public function verifPassword($password){
		$info = $this->getInfo();
		return  ($info['password'] == $password );
	}
	
	public function desinscription(){
		$sql = "DELETE FROM utilisateur WHERE id_u=?";
		$this->sqlQuery->query($sql,array($this->id_u));
	}
	
	public function setPassword($password){
		$sql = "UPDATE utilisateur SET password = ? WHERE id_u = ?";
		$this->sqlQuery->query($sql,$password,$this->id_u);
	}
	
	public function setEmail($email){
		$sql = "UPDATE utilisateur SET email = ? WHERE id_u = ?";
		$this->sqlQuery->query($sql,$email,$this->id_u);
	}
	
	public function setLogin($login){
		$sql = "UPDATE utilisateur SET login = ? WHERE id_u = ?";
		$this->sqlQuery->query($sql,$login,$this->id_u);
	}
	
}