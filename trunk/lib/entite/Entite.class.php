<?php
require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");

class Entite  {
	
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
		$info = $this->getInfo();
		return $info['entite_mere'];
	}
	
	public function getInfo(){
		if (! $this->info){
			$sql = "SELECT * FROM entite WHERE id_e=?";
			$this->info = $this->sqlQuery->fetchOneLine($sql,$this->id_e);
		}
		return $this->info;
	}
	
	public function getCDG(){
		$info = $this->getInfo();
		if ($info['centre_de_gestion']){
			return $info['centre_de_gestion'];
		}
		
		$ancetre = $this->getAncetre();
		foreach($ancetre as $id => $info){
			if ($info['centre_de_gestion']){
				return $info['centre_de_gestion'];
			}
		}
		return false;
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
		static $ancetre;
		if (! $ancetre){
			$sql = "SELECT * FROM entite_ancetre " . 
					" JOIN entite ON entite_ancetre.id_e_ancetre=entite.id_e " . 
					" WHERE entite_ancetre.id_e=? ORDER BY niveau DESC";		
			$ancetre = $this->sqlQuery->fetchAll($sql,$this->id_e);
		}
		return $ancetre;
	}
	
}