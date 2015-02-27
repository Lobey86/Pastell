<?php
class ConnecteurEntiteSQL extends SQL {
	
	public function getAll($id_e){
		$sql = "SELECT * FROM connecteur_entite";
		if (isset($id_e)) {
			$sql .= " WHERE id_e = ?";
			return $this->query($sql,$id_e);
		} else {
			return $this->query($sql);
		}
	}
	
	public function addConnecteur($id_e,$id_connecteur,$type,$libelle){
		$sql = "INSERT INTO connecteur_entite (id_e,id_connecteur,type,libelle) VALUES (?,?,?,?)";
		$this->query($sql,$id_e,$id_connecteur,$type,$libelle);
        return $this->lastInsertId();
	}
	
	public function getInfo($id_ce){
		$sql = "SELECT * FROM connecteur_entite WHERE id_ce = ?";
		return $this->queryOne($sql,$id_ce);
	}
	
	public function delete($id_ce){
		$sql = "DELETE FROM connecteur_entite WHERE id_ce=?";
		return $this->query($sql,$id_ce);
	}
	
	public function edit($id_ce,$libelle){
		$sql = "UPDATE connecteur_entite SET libelle=? WHERE id_ce=?";
		$this->query($sql,$libelle,$id_ce);
	}
	
	public function getDisponible($id_e,$type){
		$sql = "SELECT * FROM connecteur_entite WHERE type=?";
		if (isset($id_e)) {
			$sql .= " AND id_e = ?";
			return $this->query($sql,$type, $id_e);
		} else {
			return $this->query($sql,$type);
		}
	}
	
	public function getGlobal($id_connecteur){
		$sql = "SELECT id_ce FROM connecteur_entite WHERE id_connecteur = ? AND id_e=0";
		return $this->queryOne($sql,$id_connecteur);
	}
	
	public function getOne($id_connecteur){
		$sql = "SELECT id_ce FROM connecteur_entite WHERE  id_connecteur = ?";
		return $this->queryOne($sql,$id_connecteur);
	}
	
	public function getAllById($id_connecteur){
		$sql = "SELECT * FROM connecteur_entite WHERE id_connecteur = ?";
		return $this->query($sql,$id_connecteur);
	}
    
    public function getAllId() {
        $sql = "SELECT distinct id_connecteur FROM connecteur_entite WHERE id_e <>0";
        return  $this->query($sql);
    }
}