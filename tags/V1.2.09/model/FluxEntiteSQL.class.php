<?php
class FluxEntiteSQL extends SQL {
	
	
	public function getConnecteur($id_e,$flux,$connecteur_type){
		$sql = "SELECT * FROM flux_entite " .
				" JOIN connecteur_entite ON flux_entite.id_ce=connecteur_entite.id_ce " .
				" WHERE flux_entite.id_e=? AND flux=? AND flux_entite.type=?";
		
		return $this->queryOne($sql,$id_e,$flux,$connecteur_type);
	}
	
        // Selection Flux-Connecteur par l'identifiant id_fe
        public function getConnecteurById($id_fe){
		$sql = "SELECT * FROM flux_entite WHERE id_fe=?";						
		return $this->queryOne($sql,$id_fe);
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
        // Ajout de la methode getAllFluxConnecteur pour API
        public function getAllFluxEntite($id_e, $flux=null, $type=null){
		$sql = "SELECT * FROM flux_entite WHERE id_e=?";
                if ($flux) {
                    $sql = $sql . " AND flux=?";
                }
                if ($type) {
                    $sql = $sql . " AND type=?";
                }
                if ($flux && $type) {
                    $result = $this->query($sql,$id_e, $flux, $type);
                } else if ($flux) {
                    $result = $this->query($sql,$id_e, $flux);
                } else if ($type) {
                    $result = $this->query($sql,$id_e, $type);
                } else {
                    $result = $this->query($sql,$id_e);
                }
		return $result;
	}
        
	public function addConnecteur($id_e,$flux,$type,$id_ce){
		if (!$id_e){
			$flux = 'global';
		}
		$this->deleteConnecteur($id_e, $flux, $type);;
		$sql = "INSERT INTO flux_entite(id_e,flux,type,id_ce) VALUES (?,?,?,?)";
		$this->query($sql,$id_e,$flux,$type,$id_ce);
                // Ajout de la requete pour le retour sur l'API
                $sql = "SELECT id_fe FROM flux_entite WHERE id_e=? AND flux=? AND type=? AND id_ce=? ORDER BY id_fe DESC LIMIT 1";
		return $this->queryOne($sql,$id_e,$flux,$type,$id_ce);                
                
	}
	
        public function deleteConnecteur($id_e,$flux,$type){
		if (!$id_e){
			$flux = 'global';
		}
		$sql = "DELETE FROM flux_entite " .
				" WHERE id_e=? AND type=? AND flux=?";
		$this->query($sql,$id_e,$type,$flux);
	}
	
        // Nouvelle methode pour la suppression de l'association Flux-Connecteur
        public function removeConnecteur($id_fe) {
            $sql = "DELETE FROM flux_entite WHERE id_fe=?";
            $this->query($sql, $id_fe);
        }

        
	public function isUsed($id_ce){
			$sql = "SELECT flux FROM flux_entite".
				" JOIN connecteur_entite ON flux_entite.id_ce=connecteur_entite.id_ce " .
				" WHERE connecteur_entite.id_ce=?";
			return $this->queryOneCol($sql,$id_ce);
	}
	
}