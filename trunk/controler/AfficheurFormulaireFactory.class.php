<?php
class AfficheurFormulaireFactory {
	
	private $connecteurEntiteSQL;
	private $documentTypeFactory;
	private $donneesFormulaireFactory;
	
	public function __construct(ConnecteurEntiteSQL $connecteurEntiteSQL,DocumentTypeFactory $documentTypeFactory,DonneesFormulaireFactory $donneesFormulaireFactory){
		$this->connecteurEntiteSQL = $connecteurEntiteSQL;
		$this->documentTypeFactory = $documentTypeFactory;
		$this->donneesFormulaireFactory = $donneesFormulaireFactory;
	}
	
	public function getFormulaireConnecteur($id_ce){
		$connecteur_entite_info = $this->connecteurEntiteSQL->getInfo($id_ce);		
		$documentType = $this->documentTypeFactory->getEntiteDocumentType($connecteur_entite_info['id_connecteur']); 
		$donneesFormulaire = $this->donneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);		
		$afficheurFormulaire = new AfficheurFormulaire($documentType->getFormulaire(),$donneesFormulaire);
		$afficheurFormulaire->injectHiddenField("id_e",$connecteur_entite_info['id_e']);
		$afficheurFormulaire->injectHiddenField("id_ce",$connecteur_entite_info['id_ce']);
		
		return $afficheurFormulaire;
	}
	
}