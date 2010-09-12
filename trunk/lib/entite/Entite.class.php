<?php
require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");

class Entite {
	
	const DROIT_CREATION = "creer_entite";
	
	const TYPE_COLLECTIVITE = "collectivite";
	const TYPE_FOURNISSEUR = "fournisseur";
	const TYPE_CENTRE_DE_GESTION = "centre_de_gestion";
	const TYPE_SERVICE = "service";
	
	const ETAT_INITIE = 0;
	const ETAT_EN_COURS_VALIDATION = 1;
	const ETAT_VALIDE = 2;
	const ETAT_REFUSER = 3;
	const ETAT_SUSPENDU = 4;
	
	protected $sqlQuery;
	protected $siren;
	private $certificate;
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
	
	public function __construct(SQLQuery $sqlQuery,$siren){
		$this->sqlQuery = $sqlQuery;
		$this->siren = $siren;
	}
	
	public function exists(){
		return $this->getInfo();
	}
	
	public function save($denomination,$type,$entite_mere){
		$sql = "INSERT INTO entite(siren,denomination,type,date_inscription,entite_mere) " . 
				" VALUES (?,?,?,now(),?)";
		$this->sqlQuery->query($sql,array($this->siren,$denomination,$type,$entite_mere));
	}
	
	public function update($denomination,$type,$entite_mere){
		$sql = "UPDATE entite SET denomination=?,type=?,entite_mere = ?  " . 
				" WHERE siren=?";
		$this->sqlQuery->query($sql,array($denomination,$type,$entite_mere,$this->siren));
	}
	
	public function getInfo(){
		if (! $this->info){
			$sql = "SELECT * FROM entite WHERE siren=?";
			$this->info = $this->sqlQuery->fetchOneLine($sql,array($this->siren));
		}
		return $this->info;
	}
	
	public function addRole($id_u,$role){
		$sql = "INSERT INTO utilisateur_role(id_u,siren,role) VALUES (?,?,?)";
		$this->sqlQuery->query($sql,array($id_u,$this->siren,$role));
	}
	
	public function setEtat($etat){
		$sql = "UPDATE entite SET etat=? WHERE siren=?";
		$this->sqlQuery->query($sql,array($etat,$this->siren));
	}
	
	public function desinscription(){
		$info = $this->getInfo();
		if ($info['etat'] != self::ETAT_INITIE){
			return false;
		}
		$this->delete();
		return true;
	}
	
	public function delete(){
		$sql = "DELETE FROM entite WHERE siren=?";
		$this->sqlQuery->query($sql,array($this->siren));
	}

	public function getFille(){
		$sql = "SELECT * FROM entite WHERE entite_mere=?";
		return $this->sqlQuery->fetchAll($sql,array($this->siren));
	}
	
	public function getBreadCrumbs(){
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