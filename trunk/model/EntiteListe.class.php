<?php

class EntiteListe extends SQL {
	
	const NB_AFFICHABLE = 20;
	
	private $recherche;

	public function setFiltre($recherche){
		if (! $recherche){
			return;
		}
		$this->recherche = "%".$recherche."%";
	}
	
	public function countCollectivite(){
		$sql = "SELECT count(*) FROM entite WHERE type=? OR type=?" ;
		return $this->queryOne($sql,Entite::TYPE_COLLECTIVITE,Entite::TYPE_CENTRE_DE_GESTION);
	}
	
	public function getAllCollectivite($offset,$denomination){
		$result = array();
		
		$sql = "SELECT id_e,denomination,siren,type " . 
				" FROM entite WHERE entite_mere=0 AND type != 'fournisseur' AND type != 'citoyen'  " .
				" AND denomination LIKE ? ". 
				" ORDER BY denomination" .
				" LIMIT $offset,".self::NB_AFFICHABLE;
		return $this->query($sql,"%$denomination%");
	}
	
	public function getNbCollectivite($search){
		$sql = "SELECT count(*) " . 
				" FROM entite WHERE entite_mere=0 AND type != 'fournisseur' AND type != 'citoyen'  AND denomination LIKE ? " ;
		return $this->queryOne($sql,"%$search%");
	}
	
	public function getAll($type){
		$param = array($type);
		
		$sql = "SELECT * FROM entite WHERE type=? " ;
		if ($this->recherche){
			$sql .= " AND denomination LIKE ?" ;
			$param[] = $this->recherche;
		}
		
		$sql .= " ORDER BY denomination";
		return $this->query($sql,$param);
	}
	
	public function getInfoFromArray(array $tabId_e){
		$str = str_pad("",count($tabId_e),"?");
		$array = implode(',',str_split ($str));
		$sql = "SELECT id_e,siren,denomination FROM entite WHERE id_e IN ($array)";
		$result = $this->query($sql,$tabId_e);
		return $result;
	}
	
	public function getAllFille($id_e){
		$sql = "SELECT * FROM entite WHERE entite_mere=? ORDER BY denomination" ;
		return $this->query($sql,$id_e);
	}
	
	public function getAllDescendant($id_e){
		$sql = "SELECT * FROM entite_ancetre " . 
				" JOIN entite ON entite_ancetre.id_e=entite.id_e " .
				" WHERE entite_ancetre.id_e_ancetre=? AND entite.id_e!=?" . 
				" ORDER BY denomination" ;
		return $this->query($sql,$id_e,$id_e);
	}
	
	public function getDenomination($part){
		$sql = "SELECT denomination FROM `entite` WHERE denomination LIKE ? LIMIT 10";
		return $this->query($sql,"%$part%");
	}
	
	public function getBySiren($siren){
		$sql = "SELECT * FROM entite WHERE siren=?";
		return $this->query($sql,$siren);
	}
}