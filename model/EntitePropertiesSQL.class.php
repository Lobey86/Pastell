<?php


class EntitePropertiesSQL {

	const ALL_FLUX = "tous_les_flux";

	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function getProperties($id_e,$flux,$properties){
		$sql = "SELECT `values` FROM entite_properties WHERE id_e=? AND flux=? AND properties = ?";
		return $this->sqlQuery->fetchOneValue($sql,$id_e,$flux,$properties);
	}
	
	public function setProperties($id_e,$flux,$properties,$values){
		if ($this->getProperties($id_e,$flux,$properties)){
			$sql = "UPDATE entite_properties SET `values` = ? WHERE id_e=? AND flux=? AND properties= ?";
			$this->sqlQuery->query($sql,$values,$id_e,$flux,$properties);
		} else {
			$sql = "INSERT INTO entite_properties (id_e,flux,properties,`values`) VALUES (?,?,?,?)";
			$this->sqlQuery->query($sql,$id_e,$flux,$properties,$values);
		}
	}
	
}