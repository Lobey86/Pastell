<?php 
require_once( PASTELL_PATH . "/externaldata/lib/IParapheurType.class.php");

class ChoixTypeParapheurControler {

	private $lastError;
	
	public function __construct(SQLQuery $sqlQuery, DonneesFormulaireFactory $donneesFormulaireFactory){
		$this->sqlQuery = $sqlQuery; 
		$this->donneesFormulaireFactory = $donneesFormulaireFactory;
	}
	
	private function getIParapheur($id_e){
		$entite = new Entite($this->sqlQuery,$id_e);
		$ancetre = $entite->getCollectiviteAncetre();
		$donneesFormulaire = $this->donneesFormulaireFactory->get($ancetre,'collectivite-properties');
		$iParapheur = new IParapheur($donneesFormulaire);
		return $iParapheur;
	}
	
	public function isEnabled($id_e){
		return true;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function getData($id_e){
		$iParapheur = $this->getIParapheur($id_e);
		return $iParapheur->getSousType("ACTES");
	}
	
	public function get(){}
	
	public function set($id_e,$id_d,$type,Recuperateur $recuperateur){
		$iparapheurtype = $recuperateur->getInt('iparapheur_sous_type',0);
		$iParapheurType= new IParapheurType();
		$iParapheurType->setSousType($iparapheurtype,$this->sqlQuery,$this->donneesFormulaireFactory,$id_d,$id_e,$type);
	}
	
}