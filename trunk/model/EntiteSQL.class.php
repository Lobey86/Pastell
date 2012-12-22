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
	
	public function getCDG($id_e){
		return $this->getHeritedInfo($id_e,'centre_de_gestion');
	}
	
	private function getHeritedInfo($id_e,$colname){
		$info = $this->getInfo($id_e);
		if ($info[$colname]){
			return $info[$colname];
		}
		
		$ancetre = $this->getAncetre($id_e);
		foreach($ancetre as $id => $info){
			if ($info[$colname]){
				return $info[$colname];
			}
		}
		return false;
	}

	public function getExtendedInfo($id_e){
		$result = $this->getInfo($id_e);
		$cdg_id_e = $this->getCDG($id_e);
		$result['cdg'] = array();
		if ($cdg_id_e){
			$result['cdg'] = $this->getInfo($cdg_id_e) ;
		}
		if ($result['entite_mere']){
			$result['entite_mere'] = $this->getInfo($result['entite_mere']) ;
		}
		$result['filles'] = $this->getFille($id_e);
		
		return $result;
	}
	
	public function getFille($id_e){
		$sql = "SELECT * FROM entite WHERE entite_mere=? ORDER BY denomination";
		return $this->query($sql,$id_e);
	}
	

	public function getSiren($id_e){
		return $this->getHeritedInfo($id_e,'siren');
	}
	
	public function getAll(){
		$sql = "SELECT * FROM entite";
		return $this->query($sql);
	}
	
}