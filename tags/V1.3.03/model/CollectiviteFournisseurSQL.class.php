<?php
class CollectiviteFournisseurSQL extends SQL {
	
	public function add($id_e_col,$id_e_fournisseur){
		if ($this->getInfo($id_e_col, $id_e_fournisseur)){
			return;
		}
		$sql = "INSERT INTO collectivite_fournisseur(id_e_col,id_e_fournisseur) VALUES (?,?)";
		$this->query($sql,$id_e_col,$id_e_fournisseur);
	}

	public function getAllFromFournisseurId($id_e_fournisseur){
		$sql = "SELECT * FROM collectivite_fournisseur ".
				" JOIN entite ON collectivite_fournisseur.id_e_col=entite.id_e ". 
				" WHERE id_e_fournisseur=?";
		return $this->query($sql,$id_e_fournisseur);
	}
	
	public function getInfo($id_e_col,$id_e_fournisseur){
		$sql = "SELECT * FROM collectivite_fournisseur WHERE id_e_col = ? AND id_e_fournisseur=?";
		return $this->queryOne($sql,$id_e_col,$id_e_fournisseur);
	}
	
	public function validRelation($id_e_col,$id_e_fournisseur){
		$sql = "UPDATE collectivite_fournisseur SET is_valid=1 WHERE id_e_col = ? AND id_e_fournisseur=?";
		$this->query($sql,$id_e_col,$id_e_fournisseur);
	}
	
	public function getAllCollectiviteId($id_e_fournisseur){
		$sql = "SELECT id_e_col FROM collectivite_fournisseur WHERE id_e_fournisseur=? AND is_valid=true";
		return $this->queryOneCol($sql,$id_e_fournisseur);
	}
	
	public function isRelationOk($id_e_col,$id_e_fournisseur){
		$sql = "SELECT count(*) FROM collectivite_fournisseur WHERE id_e_col = ? AND id_e_fournisseur=? AND is_valid=true";
		return $this->queryOne($sql,$id_e_col,$id_e_fournisseur);
		
	}
	
}