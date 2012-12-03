<?php
class FluxEntiteSQL extends SQL {
	
	
	public function getConnecteur($id_e,$flux,$connecteur_type){
		$sql = "SELECT * FROM flux_entite " .
				" JOIN connecteur_entite ON flux_entite.id_ce=connecteur_entite.id_ce " .
				" WHERE flux_entite.id_e=? AND flux=? AND type=?";
		
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

	public function addConnecteur($id_e,$flux,$id_ce){
		$sql = "INSERT INTO flux_entite(id_e,flux,id_ce) VALUES (?,?,?)";
		$this->query($sql,$id_e,$flux,$id_ce);
	}
	
}