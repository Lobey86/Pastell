<?php
class EntiteSQL extends SQL {
	
	public function getInfo($id_e){
		$sql = "SELECT * FROM entite WHERE id_e=?";
		return $this->queryOne($sql,$id_e);
	}
	
	public function getAncetre($id_e){
		$sql = "SELECT * FROM entite_ancetre " . 
				" JOIN entite ON entite_ancetre.id_e_ancetre=entite.id_e " . 
				" WHERE entite_ancetre.id_e=? ORDER BY niveau DESC";		
		return $this->query($sql,$id_e);
	}
	
	public function getCollectiviteAncetre($id_e){
		$info = $this->getInfo($id_e);
		
		if ($info['type'] == Entite::TYPE_COLLECTIVITE || $info['type'] == Entite::TYPE_CENTRE_DE_GESTION){
			return $id_e;
		}
		foreach($this->getAncetre($id_e) as $ancetre){
			if ($ancetre['type'] == Entite::TYPE_COLLECTIVITE){
				return $ancetre['id_e'];
			}
		}
		return false;
	}
	
	
	
}