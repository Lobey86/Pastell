<?php
class FluxEntiteSQL extends SQL {
	
	
	public function getConnecteur($id_e,$flux,$connecteur_type){
		$sql = "SELECT * FROM flux_entite " .
				" JOIN connecteur_entite ON flux_entite.id_ce=connecteur_entite.id_ce " .
				" WHERE flux_entite.id_e=? AND flux=? AND flux_entite.type=?";
		
		return $this->queryOne($sql,$id_e,$flux,$connecteur_type);
	}
	
	public function getAll($id_e){
		$sql = "SELECT * FROM flux_entite".
				" JOIN connecteur_entite ON flux_entite.id_ce=connecteur_entite.id_ce " .
				" WHERE flux_entite.id_e=?";
		$result = array();
		foreach($this->query($sql,$id_e) as $line){
			$result[$line['flux']][$line['type']] = $line;
		}
		return $result;
	}

	public function addConnecteur($id_e,$flux,$type,$id_ce){
		if (!$id_e){
			$flux = 'global';
		}
		$this->deleteConnecteur($id_e, $flux, $type);
		//$this->query($sql,$id_e,$type,$flux);
		$sql = "INSERT INTO flux_entite(id_e,flux,type,id_ce) VALUES (?,?,?,?)";
		$this->query($sql,$id_e,$flux,$type,$id_ce);
	}
	
	public function deleteConnecteur($id_e,$flux,$type){
		if (!$id_e){
			$flux = 'global';
		}
		$sql = "DELETE FROM flux_entite " .
				" WHERE id_e=? AND type=? AND flux=?";
		$this->query($sql,$id_e,$type,$flux);
	}
	
	public function isUsed($id_ce){
			$sql = "SELECT flux FROM flux_entite".
				" JOIN connecteur_entite ON flux_entite.id_ce=connecteur_entite.id_ce " .
				" WHERE connecteur_entite.id_ce=?";
			return $this->queryOneCol($sql,$id_ce);
	}
	
}