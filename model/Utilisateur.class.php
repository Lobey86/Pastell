<?php
class Utilisateur {

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
	
	public function setColBase($id_e){
		$sql = "UPDATE utilisateur SET id_e = ? WHERE id_u = ?";
		$this->sqlQuery->query($sql,$id_e,$this->id_u);
	}
	
	public function removeCertificat(){
		$this->updateCertificat("","");
	}
	
	public function setCertificat(Certificat $certificat){
		
		if (! $certificat->isValid()){
			return false;
		}
		
		$certificatContent = $certificat->getContent();
		$certificatVerifNumber = $certificat->getVerifNumber();
		
		$this->updateCertificat($certificatContent,$certificatVerifNumber);
		return true;
	}
	
	private function updateCertificat($content,$verif_number){
		$sql = "UPDATE utilisateur SET certificat = ?, certificat_verif_number=? WHERE id_u = ?";
		$this->sqlQuery->query($sql,$content,$verif_number,$this->id_u);
	}
	
	public function reinitPassword($mailVerifPassword){
		$sql = "UPDATE utilisateur SET mail_verif_password=? WHERE id_u=?";
		$this->sqlQuery->query($sql,$mailVerifPassword,$this->id_u);
	}
	
}