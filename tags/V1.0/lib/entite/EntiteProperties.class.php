<?php


class EntiteProperties {

	const ALL_FLUX = "tous_les_flux";

	public function __construct($sqlQuery,$id_e){
		$this->sqlQuery = $sqlQuery;
		$this->id_e = $id_e;
	}
	
	public function getProperties($flux,$properties){
		$sql = "SELECT `values` FROM entite_properties WHERE id_e=? AND flux=? AND properties = ?";
		return $this->sqlQuery->fetchOneValue($sql,$this->id_e,$flux,$properties);
	}
	
	public function setProperties($flux,$properties,$values){
		if ($this->getProperties($flux,$properties)){
			$sql = "UPDATE entite_properties SET `values` = ? WHERE id_e=? AND flux=? AND properties= ?";
			$this->sqlQuery->query($sql,$values,$this->id_e,$flux,$properties);
		} else {
			$sql = "INSERT INTO entite_properties (id_e,flux,properties,`values`) VALUES (?,?,?,?)";
			$this->sqlQuery->query($sql,$this->id_e,$flux,$properties,$values);
		}
	}
	
}