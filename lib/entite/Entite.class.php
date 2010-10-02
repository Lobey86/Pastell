<?php
require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");

class Entite  {
		
	const CREATE_SQL = "CREATE TABLE entite (
  id_e int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL,
  denomination varchar(128) NOT NULL,
  siren char(9) NOT NULL,
  date_inscription datetime NOT NULL,
  etat int(11) NOT NULL,
  entite_mere varchar(9) DEFAULT NULL,
  PRIMARY KEY (id_e)
)";
	
	const TYPE_COLLECTIVITE = "collectivite";
	const TYPE_FOURNISSEUR = "fournisseur";
	const TYPE_CENTRE_DE_GESTION = "centre_de_gestion";
	const TYPE_SERVICE = "service";
	
	const ETAT_INITIE = 0;
	const ETAT_EN_COURS_VALIDATION = 1;
	const ETAT_VALIDE = 2;
	const ETAT_REFUSER = 3;
	const ETAT_SUSPENDU = 4;
	
	private $sqlQuery;
	
	private $id_e;
	
	private $info;
	
	public static function getNom($type){
		$type_nom = array(self::TYPE_COLLECTIVITE => "Collectivité", 
							self::TYPE_FOURNISSEUR => "Fournisseur",
							self::TYPE_CENTRE_DE_GESTION => "Centre de gestion",
							self::TYPE_SERVICE => 'Service');
		return $type_nom[$type];
	}
	
	public static function getChaineEtat($etat){
		$strEtat = array("Initié","En cours de validation","Validé", "Refusé","Suspendu");
		return $strEtat[$etat];
	}
	
	public function __construct(SQLQuery $sqlQuery,$id_e){
		$this->sqlQuery = $sqlQuery;
		$this->id_e = $id_e;
	}
	

	public function exists(){
		return $this->getInfo();
	}
	
	public function getMere(){
		return $this->sqlQuery->fetchOneValue("SELECT entite_mere FROM entite WHERE id_e=?",$this->id_e);
	}
	
	public function getInfo(){
		if (! $this->info){
			$sql = "SELECT * FROM entite WHERE id_e=?";
			$this->info = $this->sqlQuery->fetchOneLine($sql,$this->id_e);
		}
		return $this->info;
	}
	
	//TODO => mettre dans EntiteModifier ?
	public function setEtat($etat){
		$sql = "UPDATE entite SET etat=? WHERE id_e=?";
		$this->sqlQuery->query($sql,$etat,$this->id_e);
	}
	
	public function desinscription(){
		$info = $this->getInfo();
		if ($info['etat'] != self::ETAT_INITIE){
			return false;
		}
		$this->delete();
		return true;
	}
	
	public function getFille(){
		$sql = "SELECT * FROM entite WHERE entite_mere=? ORDER BY denomination";
		return $this->sqlQuery->fetchAll($sql,$this->id_e);
	}
	
	public function getAncetre(){
		$info = $this->getInfo();
		$bc = array();
		while ($info['entite_mere']){
			$entite = new Entite($this->sqlQuery,$info['entite_mere']);
			$info = $entite->getInfo();
			$bc[] = $info;
		}
		
		return array_reverse($bc);
	}
	
	public function getBreadCrumbs(){
		if (! $this->exists()){
			return array();
		}
		$info = $this->getInfo();
		$bc = array($info);
		while ($info['entite_mere']){
			$entite = new Entite($this->sqlQuery,$info['entite_mere']);
			$info = $entite->getInfo();
			$bc[] = $info;
		}
		
		return array_reverse($bc);
	}
	
	
}