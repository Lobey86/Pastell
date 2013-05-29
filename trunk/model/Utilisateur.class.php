<?php

class Utilisateur extends SQL {

	public function setNomPrenom($id_u,$nom,$prenom){
		$sql = "UPDATE utilisateur SET nom = ? , prenom = ? WHERE id_u = ?";
		$this->query($sql,array($nom,$prenom,$id_u));
	}
	
	public function getInfo($id_u){
		$sql = "SELECT * FROM utilisateur WHERE id_u = ?";
		return $this->queryOne($sql,$id_u);
	}
	
	public function validMail($id_u,$password){
		$sql = "SELECT id_u FROM utilisateur " . 
				" WHERE id_u =? AND mail_verif_password= ? ";
		$result = $this->queryOne($sql,$id_u, $password);
		if ( ! $result){
			return false;
		}
		$this->validMailAuto($id_u);
		return true;
	}
	
	public function validMailAuto($id_u){
		$sql = "UPDATE utilisateur SET mail_verifie=1 WHERE id_u=?";
		$this->query($sql,$id_u);
	}
	
	public function verifPassword($id_u,$password){
		$info = $this->getInfo($id_u);
		return crypt($password, $info['password']) == $info['password'];
	}
	
	public function desinscription($id_u){
		$sql = "DELETE FROM utilisateur WHERE id_u=?";
		$this->query($sql,$id_u);
	}
	
	public function setPassword($id_u,$password){
		$sql = "UPDATE utilisateur SET password = ? WHERE id_u = ?";
		$this->query($sql,crypt($password),$id_u);
	}
	
	public function setEmail($id_u,$email){
		$sql = "UPDATE utilisateur SET email = ? WHERE id_u = ?";
		$this->query($sql,$email,$id_u);
	}
	
	public function setLogin($id_u,$login){
		$sql = "UPDATE utilisateur SET login = ? WHERE id_u = ?";
		$this->query($sql,$login,$id_u);
	}
	
	public function setColBase($id_u,$id_e){
		$sql = "UPDATE utilisateur SET id_e = ? WHERE id_u = ?";
		$this->query($sql,$id_e,$id_u);
	}
	
	public function removeCertificat($id_u){
		$this->updateCertificat($id_u,"","");
	}
	
	public function setCertificat($id_u,Certificat $certificat){
		
		if (! $certificat->isValid()){
			return false;
		}
		
		$certificatContent = $certificat->getContent();
		$certificatVerifNumber = $certificat->getVerifNumber();
		
		$this->updateCertificat($id_u,$certificatContent,$certificatVerifNumber);
		return true;
	}
	
	private function updateCertificat($id_u,$content,$verif_number){
		$sql = "UPDATE utilisateur SET certificat = ?, certificat_verif_number=? WHERE id_u = ?";
		$this->query($sql,$content,$verif_number,$id_u);
	}
	
	public function reinitPassword($id_u,$mailVerifPassword){
		$sql = "UPDATE utilisateur SET mail_verif_password=? WHERE id_u=?";
		$this->query($sql,$mailVerifPassword,$id_u);
	}

	public function getIdFromLogin($login){
		$sql = "SELECT id_u FROM utilisateur WHERE login = ?";
		return $this->queryOne($sql,$login);
	}
	
	public function create($login,$password,$email,$password_validation){
		$sql = "INSERT INTO utilisateur(login,email,mail_verif_password,date_inscription) " . 
				" VALUES (?,?,?,now())";
		$this->query($sql,$login,$email,$password_validation);
		$id_u =  $this->getIdFromLogin($login);
		$this->setPassword($id_u, $password);
		return $id_u;
	}
	
	
	
}