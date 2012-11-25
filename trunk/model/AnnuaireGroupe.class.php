<?php
class AnnuaireGroupe extends SQL {
	
	const NB_MAX = 5;
	
	private $id_e;
	
	public function __construct(SQLQuery $sqlQuery,$id_e){
		parent::__construct($sqlQuery);
		$this->id_e = $id_e;
	}
	
	public function getInfo($id_g){
		$sql = "SELECT * FROM annuaire_groupe WHERE id_e=? AND id_g=?";
		return $this->queryOne($sql,$this->id_e,$id_g);
	}
	
	public function getGroupe(){
		$sql = "SELECT * FROM annuaire_groupe WHERE id_e=?";
		return $this->query($sql,$this->id_e);
	}
	
	public function getFromNom($nom){
		$sql = "SELECT id_g FROM annuaire_groupe WHERE id_e=? AND nom=?";
		return $this->queryOne($sql,$this->id_e,$nom);
	}
	
	public function add($nom){
		$id_g = $this->getFromNom($nom);
		if ( ! $id_g){
			$sql = "INSERT INTO annuaire_groupe (id_e,nom) VALUES (?,?)";
			$this->query($sql,$this->id_e,$nom);
		}
	}
	
	public function getNbUtilisateur($id_g){
		$sql = "SELECT count(*) FROM annuaire_groupe_contact WHERE id_g=?";
		return $this->queryOne($sql,$id_g);
	}
	
	public function getAllUtilisateur($id_g){
		$sql = "SELECT * FROM annuaire_groupe_contact " . 
		" JOIN annuaire ON annuaire_groupe_contact.id_a=annuaire.id_a " .
		" WHERE id_g=? ";
		return $this->query($sql,$id_g);
	}
	
	public function getUtilisateur($id_g,$offset = 0){
		$sql = "SELECT * FROM annuaire_groupe_contact " . 
				" JOIN annuaire ON annuaire_groupe_contact.id_a=annuaire.id_a " .
				" WHERE id_g=? LIMIT $offset,".self::NB_MAX;
		return $this->query($sql,$id_g);
	}
	
	public function delete(array $lesId_g){
		foreach ($lesId_g as $id_g){
			$sql = "DELETE FROM annuaire_groupe WHERE id_e = ? AND id_g=?";
			$this->query($sql,$this->id_e,$id_g);
		}
	}
	
	public function isInGroupe($id_g,$id_a){
		$sql = "SELECT count(*) FROM annuaire_groupe_contact WHERE  id_g=? AND id_a=? ";
		return $this->queryOne($sql,$id_g,$id_a);
	}
	
	public function addToGroupe($id_g,$id_a){
		if ($this->isInGroupe($id_g,$id_a)){
			return;
		}
		$sql = "INSERT INTO annuaire_groupe_contact (id_g,id_a) VALUES (?,?)";
		$this->query($sql,$id_g,$id_a);
	}
	
	public function deleteFromGroupe($id_g,array $id_aList){
		foreach ($id_aList as $id_a){
			$sql = "DELETE FROM annuaire_groupe_contact WHERE id_g=? AND id_a=?";
			$this->query($sql,$id_g,$id_a);
		}
	}
	
	public function getListGroupe($debut){
		$sql = "SELECT nom FROM annuaire_groupe WHERE id_e=? AND nom LIKE ?";
		return $this->query($sql,$this->id_e,"$debut%");
	}
	
	public function tooglePartage($id_g){
		$sql = "UPDATE annuaire_groupe SET partage = 1 - partage WHERE id_g=?";
		$this->query($sql,$id_g);
	}
	
	public function getGroupeHerite($all_ancetre,$debut = ""){
		$result = array();
		foreach($all_ancetre as $id_e){
			$sql = "SELECT annuaire_groupe.*,entite.denomination FROM annuaire_groupe " .
					" LEFT JOIN entite ON annuaire_groupe.id_e = entite.id_e".
					" WHERE annuaire_groupe.id_e=? AND partage=1";
			$data = array($id_e);
			if($debut){
				$sql.= " AND nom LIKE ?";
				$data[] = "$debut%";
			}
			$all_g = $this->query($sql,$data);
			if ($all_g){
				$result = array_merge($result,$all_g );
			}
		}
		return $result;
	}
	
	public function getChaineHerited($info){
		if ($info['denomination']){
			$debut = "groupe hérité de {$info['denomination']}";
		} else {
			$debut = "groupe global";
		}
		
		return "$debut: \"".$info['nom'] . "\"";
	}
	
	public function getFromNomDenomination($all_ancetre,$chaine){
		foreach($this->getGroupeHerite($all_ancetre) as $info){
			if ($chaine == $this->getChaineHerited($info)){
				return $info['id_g'];
			}
		}
		return false;
	}
	
}