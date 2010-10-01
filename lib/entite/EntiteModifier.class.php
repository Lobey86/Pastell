<?php


class EntiteModifier {
	
	
	public function __construct($sqlQuery,$journal,$id_e){
		$this->sqlQuery = $sqlQuery;
		$this->journal = $journal;
		$this->id_e = $id_e;
	}
	
	
	public function update($siren,$denomination,$type,$entite_mere = 0){
		$sql = "UPDATE entite SET siren= ? , denomination=?,type=?,entite_mere = ?  " . 
				" WHERE id_e=?";
		$this->sqlQuery->query($sql,$siren,$denomination,$type,$entite_mere,$this->id_e);
		$this->journal->add(Journal::MODIFICATION_ENTITE,$this->id_e,0,"modification","");	
	}

	public function setCentreDeGestion($id_e){
		$sql = "UPDATE entite SET centre_de_gestion=? WHERE id_e=?";
		$this->sqlQuery->query($sql,$id_e,$this->id_e);
		$this->journal->add(Journal::MODIFICATION_ENTITE,$this->id_e,0,"modification","mise à jour du centre de gestion : $id_e");	
	}
	
	
}