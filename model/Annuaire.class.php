<?php

class Annuaire extends SQL {
	
	private $id_e;
	
	public function __construct(SQLQuery $sqlQuery,$id_e){
		parent::__construct($sqlQuery);
		$this->id_e = $id_e;
	}
	
	public function getUtilisateur(){
//		$sql = "SELECT * FROM annuaire WHERE id_e=?";
		$sql = "SELECT * FROM annuaire WHERE id_e=? ORDER BY description ASC";
		return $this->query($sql,$this->id_e);
	}
	
	public function getFromEmail($email){
		$sql = "SELECT id_a FROM annuaire WHERE id_e=? AND email=? ORDER BY email ASC";
		return $this->queryOne($sql,$this->id_e,$email);
	}
	
	public function add($description,$email){
		$id_a = $this->getFromEmail($email);
		
		if ($id_a){
			$sql = "UPDATE annuaire SET description=? WHERE id_e=? AND email= ?";
			$this->query($sql,$description,$this->id_e,$email);
		} else {
			$sql = "INSERT INTO annuaire (id_e,description,email) VALUES (?,?,?)";
			$this->query($sql,$this->id_e,$description,$email);
		}
	}
	
	public function delete(array $lesEmails){
		foreach ($lesEmails as $email){
			$sql = "DELETE FROM annuaire WHERE id_e = ? AND email=?";
			$this->query($sql,$this->id_e,$email);
		}
	}
	
	public function getListeMail($debut){
		$sql = "SELECT description,email FROM annuaire ".
				" WHERE (email LIKE ? OR description LIKE ?) AND id_e = ? " .
				" ORDER by description,email";
		return $this->query($sql,"$debut%","$debut%",$this->id_e);
	}
	
}