<?php 

class ChoixTypeParapheurControler {

	private $lastError;
	
	private $formulaire;
	
	public function __construct(SQLQuery $sqlQuery, DonneesFormulaireFactory $donneesFormulaireFactory, Formulaire $formulaire = null){
		$this->sqlQuery = $sqlQuery; 
		$this->donneesFormulaireFactory = $donneesFormulaireFactory;
		$this->formulaire = $formulaire;
	}
	
	private function getIParapheur($id_e){
		$entite = new Entite($this->sqlQuery,$id_e);
		$ancetre = $entite->getCollectiviteAncetre();
		$donneesFormulaire = $this->donneesFormulaireFactory->getEntiteFormulaire($ancetre);
		//TODO !!!
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
		
		$type_iparapheur = $this->formulaire->getField('iparapheur_type')->getProperties('default');
		$iParapheur = $this->getIParapheur($id_e);
		
		return $iParapheur->getSousType($type_iparapheur);
	}
	
	public function get(){}
	
	public function set($id_e,$id_d,$type,Recuperateur $recuperateur){
		$iparapheurtype = $recuperateur->getInt('iparapheur_sous_type',0);
		$iParapheurType= new IParapheurType();
		$iParapheurType->setSousType($iparapheurtype,$this->sqlQuery,$this->donneesFormulaireFactory,$id_d,$id_e,$type);
	}
	
}