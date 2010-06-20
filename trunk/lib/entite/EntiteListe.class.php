<?php

require_once("Entite.class.php");
class EntiteListe {
	
	private $sqlQuery;
	private $recherche;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function setFiltre($recherche){
		if (! $recherche){
			return;
		}
		$this->recherche = "%".$recherche."%";
	}
	
	public function getAll($type){
		$param = array($type);
		
		$sql = "SELECT * FROM entite WHERE type=? " ;
		if ($this->recherche){
			$sql .= " AND denomination LIKE ?" ;
			$param[] = $this->recherche;
		}
		
		$sql .= " ORDER BY denomination";
		
		
		return $this->sqlQuery->fetchAll($sql,$param);
	}
	
	public function getInfoFromArray(array $les_siren){
		$str = str_pad("",count($les_siren),"?");
		$array = implode(',',str_split ($str));
		$sql = "SELECT siren,denomination FROM entite WHERE siren IN ($array)";
		$result = $this->sqlQuery->fetchAll($sql,$les_siren);
		return $result;
	}
	
	public function getAllPossibleMother(){
		$sql = "SELECT * FROM entite WHERE type != ?";
		return $this->sqlQuery->fetchAll($sql,array(Entite::TYPE_FOURNISSEUR));
	}
	
	
}