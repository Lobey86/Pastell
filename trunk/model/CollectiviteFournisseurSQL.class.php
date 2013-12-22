<?php
class CollectiviteFournisseurSQL extends SQL {
	
	public function add($id_e_col,$id_e_fournisseur){
		$sql = "SELECT count(*) FROM collectivite_fournisseur WHERE id_e_col = ? AND id_e_fournisseur=?";
		if ($this->queryOne($sql,$id_e_col,$id_e_fournisseur)){
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
	
}