<?php
class AnnuaireRoleSQL extends SQL {
	
	private $roleUtilisateur;
	
	public function __construct(SQLQuery $sqlQuery,RoleUtilisateur $roleUtilisateur){
		parent::__construct($sqlQuery);
		$this->roleUtilisateur = $roleUtilisateur;
	}
	
	public function getAll($id_e){
		$sql = "SELECT * FROM annuaire_role WHERE id_e_owner=? ORDER BY nom ASC";
		return $this->query($sql,$id_e);
	}
	
	public function add($nom,$id_e_owner,$id_e,$role){
		$sql = "INSERT INTO annuaire_role(nom,id_e_owner,id_e,role) VALUES (?,?,?,?)";
		$this->query($sql,$nom,$id_e_owner,$id_e,$role);
	}
	
	public function getUtilisateur($id_r){
		$sql = "SELECT * FROM annuaire_role WHERE id_r=?";
		$info = $this->queryOne($sql,$id_r);
		return $this->roleUtilisateur->getAllUtilisateurHerite($info['id_e'],$info['role']);
	}
	
	public function getInfo($id_r){
		$sql = "SELECT * FROM annuaire_role WHERE id_r=?";
		return $this->queryOne($sql,$id_r);
	}
	
	public function delete($id_r){
		$sql = "DELETE FROM annuaire_role WHERE id_r=?";
		$this->query($sql,$id_r);
	}
	
	public function getList($id_e,$debut){
		$sql = "SELECT nom FROM annuaire_role WHERE id_e_owner=? AND nom LIKE ?";
		return $this->query($sql,$id_e,"$debut%");
	}
	
	public function getFromNom($id_e,$nom){
		$sql = "SELECT id_r FROM annuaire_role WHERE id_e_owner=? AND nom=?";
		return $this->queryOne($sql,$id_e,$nom);
	}
	
	public function partage($id_r){
		$sql= "UPDATE annuaire_role SET partage=1 WHERE id_r=?";
		$this->query($sql,$id_r);
	}
	
	public function unpartage($id_r){
		$sql= "UPDATE annuaire_role SET partage=0 WHERE id_r=?";
		$this->query($sql,$id_r);
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
			$all_g = $this->query($sql,$data);
			if ($all_g){
				$result = array_merge($result,$all_g );
			}
		}
		return $result;
	}
	
	public function getChaineHerited($info){
		if ($info['denomination']){
			$debut = "rôle hérité de {$info['denomination']}";
		} else {
			$debut = "rôle global";
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