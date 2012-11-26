<?php
require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

class IparapheurType {
	
	private $type;
	
	public function isEnabled($sqlQuery,$id_e,DonneesFormulaireFactory $donneesFormulaireFactory){
		$entite = new Entite($sqlQuery,$id_e);
		$ancetre = $entite->getCollectiviteAncetre();
		$donneesFormulaire = $donneesFormulaireFactory->getEntiteFormulaire($ancetre);
		$result = $donneesFormulaire->get('iparapheur_activate');
		return $result;
	}
	
	private function getIParapheur($sqlQuery,$id_e,$donneesFormulaireFactory,$type,$id_d){
		$entite = new Entite($sqlQuery,$id_e);
		$ancetre = $entite->getCollectiviteAncetre();
		$donneesFormulaire = $donneesFormulaireFactory->getEntiteFormulaire($ancetre);
		
		$iParapheur = new IParapheur($donneesFormulaire);
		
		$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
		$this->type = $donneesFormulaire->get('iparapheur_type');
		
		return $iParapheur;
	}
	
	private function display($id_d,$id_e,$page,$field,$iparapheur_type){
		?>
			<form action='document/external-data-controler.php' method='post'>
				<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
				<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
				<input type='hidden' name='page' value='<?php echo $page?>' />
				<input type='hidden' name='field' value='<?php echo $field?>' />
				
				<select name='iparapheur_sous_type'>
				<?php foreach($iparapheur_type as $num => $type_message) : ?>
					<option value='<?php echo $num?>'><?php echo $type_message?></option>
				<?php endforeach; ?>
				</select>	
				<input type='submit' value='Sélectionner'/>
			</form>
		<?php 
	}
	
	public function displayType($sqlQuery,$donneesFormulaireFactory,$id_d,$id_e,$page,$field,$type){
		$iParapheur = $this->getIParapheur($sqlQuery,$id_e,$donneesFormulaireFactory,$type,$id_d);
		$iparapheur_type = $iParapheur->getType()->TypeTechnique;		
		$this->display($id_d,$id_e,$page,$field,$iparapheur_type);
	}
	
	public function displaySousType($sqlQuery,$donneesFormulaireFactory,$id_d,$id_e,$page,$field,$type){
		$iParapheur = $this->getIParapheur($sqlQuery,$id_e,$donneesFormulaireFactory,$type,$id_d);
		$iparapheur_type = $iParapheur->getSousType($this->type);
		
		$this->display($id_d,$id_e,$page,$field,$iparapheur_type);
	}
	
	
	public function setSousType($iparapheurtype,$sqlQuery,$donneesFormulaireFactory,$id_d,$id_e,$type){
		$iParapheur = $this->getIParapheur($sqlQuery,$id_e,$donneesFormulaireFactory,$type,$id_d);
		$iparapheur_type = $iParapheur->getSousType($this->type);
		
		if (empty($iparapheur_type[$iparapheurtype])){
			$this->lastError = "Ce type n'existe pas";
			exit;
		}
		
		$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
		$donneesFormulaire->setData('iparapheur_sous_type',$iparapheur_type[$iparapheurtype]);		
	}
	
	public function setType($donneesFormulaireFactory, $id_d,$type_document,$type_iparapheur){
		$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type_document);
		$donneesFormulaire->setData('iparapheur_type',$type_iparapheur);
	}
	
	
	
}