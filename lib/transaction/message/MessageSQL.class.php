<?php

require_once( ZEN_PATH . "/lib/Date.class.php");

class MessageSQL {

	public function __construct($sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function create($id_t,$type,$emetteur,$message){
		$now = date(Date::DATE_ISO);
		$sql = "INSERT INTO message(id_t,type,emetteur,message,date_envoie) VALUES (?,?,?,?,?)";
		$this->sqlQuery->query($sql,array($id_t,$type,$emetteur,$message,$now));
		$sql = "SELECT id_m FROM message WHERE id_t=? AND type=? AND emetteur=? AND  date_envoie=? ";
		return $this->sqlQuery->fetchOneValue($sql,array($id_t,$type,$emetteur,$now));
	}
	
	public function addDestinataire($id_m,$destinataire){
		$sql = "INSERT INTO message_destinataire(id_m,siren) VALUES (?,?)";
		$this->sqlQuery->query($sql,array($id_m,$destinataire));
	}
	
	public function getMessageFromTransaction($id_t){
		$sql = "SELECT message.*,entite.denomination,entite.siren from message JOIN entite ON emetteur=siren WHERE id_t = ? ORDER BY date_envoie DESC";
		return $this->sqlQuery->fetchAll($sql,array($id_t));
	}
	
	public function addRessource($id_m,$ressource_path,$type = "file",$orig_filename=""){
		$sql  = "INSERT INTO message_ressource (id_m,ressource,type,original_name) VALUES (?,?,?,?)";
		$this->sqlQuery->query($sql,array($id_m,$ressource_path,$type,$orig_filename));
	}
	
	public function restrictInformation($siren){
		//TODO : cette focntion permet de restreindre la récuperation des mails
		// pour une entite (ex : fournisseur)
	}
	
	public function getDestinataire($id_m){
		$sql = "SELECT entite.* FROM message_destinataire  " . 
				" JOIN entite ON message_destinataire.siren=entite.siren " . 
				" WHERE id_m=?";
		return $this->sqlQuery->fetchAll($sql,array($id_m));
	}
	
	public function getRessource($id_m){
		return $this->sqlQuery->fetchAll("SELECT * FROM message_ressource WHERE id_m = ?  ",array($id_m));
	}
	
	//TODO : sous optimale
	public function getAllEntite($id_t){
		$result = array();
		$transaction = $this->getMessageFromTransaction($id_t);
		foreach($transaction as $t){
			$result[$t['emetteur']] = $t['denomination'];
			$destinataire = $this->getDestinataire($t['id_m']);
			foreach($destinataire as $dest){
				$result[$dest['siren']] = $dest['denomination'];
			}
		}
		return $result;	
	}
	
	public function getInfo($id_m){
		$sql = "SELECT * FROM message WHERE id_m=?";
		return $this->sqlQuery->fetchOneLine($sql,array($id_m));
	}
	
	
}