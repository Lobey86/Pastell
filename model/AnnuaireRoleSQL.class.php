<?php
class AnnuaireRoleSQL {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getAll($id_e){
		$sql = "SELECT * FROM annuaire_role WHERE id_e_owner=?";
		return $this->sqlQuery->fetchAll($sql,$id_e);
	}
	
	public function add($nom,$id_e_owner,$id_e,$role){
		$sql = "INSERT INTO annuaire_role(nom,id_e_owner,id_e,role) VALUES (?,?,?,?)";
		$this->sqlQuery->query($sql,$nom,$id_e_owner,$id_e,$role);
	}
	
	public function getUtilisateur($id_r){
		$sql = "SELECT * FROM annuaire_role WHERE id_r=?";
		$info = $this->sqlQuery->fetchOneLine($sql,$id_r);
		$roleUtilisateur = new RoleUtilisateur($this->sqlQuery);
		return $roleUtilisateur->getAllUtilisateurHerite($info['id_e'],$info['role']);
	}
	
	public function getInfo($id_r){
		$sql = "SELECT * FROM annuaire_role WHERE id_r=?";
		return $this->sqlQuery->fetchOneLine($sql,$id_r);
	}
	
	public function delete($id_r){
		$sql = "DELETE FROM annuaire_role WHERE id_r=?";
		$this->sqlQuery->query($sql,$id_r);
	}
	
	public function getList($id_e,$debut){
		$sql = "SELECT nom FROM annuaire_role WHERE id_e_owner=? AND nom LIKE ?";
		return $this->sqlQuery->fetchAll($sql,$id_e,"$debut%");
	}
	
	public function getFromNom($id_e,$nom){
		$sql = "SELECT id_r FROM annuaire_role WHERE id_e_owner=? AND nom=?";
		return $this->sqlQuery->fetchOneValue($sql,$id_e,$nom);
	}
	
	public function partage($id_r){
		$sql= "UPDATE annuaire_role SET partage=1 WHERE id_r=?";
		$this->sqlQuery->query($sql,$id_r);
	}
	
	public function unpartage($id_r){
		$sql= "UPDATE annuaire_role SET partage=0 WHERE id_r=?";
		$this->sqlQuery->query($sql,$id_r);
	}
	
	public function getGroupeHerite($all_ancetre,$debut = ""){
		$result = array();
		foreach($all_ancetre as $id_e){
			$sql = "SELECT annuaire_role.*,entite.denomination FROM annuaire_role " .
					" LEFT JOIN entite ON annuaire_role.id_e_owner = entite.id_e".
					" WHERE annuaire_role.id_e_owner=? AND partage=1";
			$data = array($id_e);
			if($debut){
				$sql.= " AND nom LIKE ?";
				$data[] = "$debut%";
			}
			$all_g = $this->sqlQuery->fetchAll($sql,$data);
			if ($all_g){
				$result = array_merge($result,$all_g );
			}
		}
		return $result;
	}
	
	public function getChaineHerited($info){
		if ($info['denomination']){
			$debut = "r�le h�rit� de {$info['denomination']}";
		} else {
			$debut = "r�le global";
		}
		
		return "$debut: \"".$info['nom'] . "\"";
	}
	
	public function getFromNomDenomination($all_ancetre,$chaine){
		foreach($this->getGroupeHerite($all_ancetre) as $info){
			if ($chaine == $this->getChaineHerited($info)){
				return $info['id_r'];
			}
		}
		return false;
	}
	
}