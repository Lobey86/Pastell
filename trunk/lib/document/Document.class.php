<?php

class Document {
	
	const MAX_ESSAI = 5;
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
		$this->setPasswordGenerator(new PasswordGenerator());
	}
	
	public function setPasswordGenerator(PasswordGenerator $passwordGenerator){
		$this->passwordGenerator = $passwordGenerator;
	}
	
	public function getNewId(){
		for ($i=0; $i<self::MAX_ESSAI; $i++){
			$id_d = $this->passwordGenerator->getPassword();
			$sql = "SELECT count(*) FROM document WHERE id_d=?";
			$nb = $this->sqlQuery->fetchOneValue($sql,$id_d);
			
			if ($nb == 0){
				return $id_d;
			}	
		}
		throw new Exception("Impossible de trouver un numéro de transaction");
	}
	
	public function save($id_d,$type){
		$sql = "INSERT INTO document(id_d,type,creation,modification) VALUES (?,?,now(),now())";
		$this->sqlQuery->query($sql,$id_d,$type);
	}
	
	public function setTitre($id_d,$titre){
		$sql = "UPDATE document SET titre = ?,modification=now() WHERE id_d = ?";
		$this->sqlQuery->query($sql,$titre,$id_d);
	}
	
	public function getInfo($id_d){
		$sql = "SELECT * FROM document WHERE id_d = ? ";
		return $this->sqlQuery->fetchOneLine($sql,$id_d);
	}
	
	public function getIdFromTitre($titre,$type){		
		$sql = "SELECT id_d FROM document WHERE titre=? AND type=?";
		return $this->sqlQuery->fetchOneValue($sql,$titre,$type);
	}
	
}