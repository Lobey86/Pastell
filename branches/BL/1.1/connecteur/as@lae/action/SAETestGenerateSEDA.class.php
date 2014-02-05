<?php

require_once( PASTELL_PATH . "/module/actes/lib/ActesArchiveSEDA.class.php");

class SAETestGenerateSEDA extends ActionExecutor {
	
	public function go(){
		$donneesFormulaire = $this->getConnecteurProperties();
		if (! $donneesFormulaire->get("sae_activate")){
			$this->setLastMessage("Le module n'est pas activé");
			return false;	
		}
		
		$entite = $this->getEntite();
		$entiteInfo = $entite->getInfo();
		
		$authorityInfo = array(
			"sae_id_versant" =>  $donneesFormulaire->get("sae_identifiant_versant"),
			"sae_id_archive" =>  $donneesFormulaire->get("sae_identifiant_archive"),
			"sae_numero_aggrement" =>  $donneesFormulaire->get("sae_numero_agrement"),
			"sae_originating_agency" =>  $donneesFormulaire->get("sae_originating_agency"),
			"name" =>  $entiteInfo['denomination'],
			"siren" => $entiteInfo['siren'],
		);
		$uniq_id = time();
		$actesTransactionsStatusInfo = array(
			'transaction_id' => $uniq_id,
			'flux_retour' => '<empty></empty>',
			'date' => date("Y-m-d")
		);
		
		$transactionsInfo = array(
			'unique_id' => $uniq_id,
			'subject' => "bordereau de test",
			'decision_date' => '2012-02-18',
			'nature_descr' => 'Arretes individuels',
			'nature_code' => '1',
			'classification' => '1.1',
		);
		
		$actesArchivesSEDA = new ActesArchiveSEDA("/tmp/");
		$actesArchivesSEDA->setAuthorityInfo($authorityInfo);
		copy(__DIR__."/../data-exemple/exemple.pdf","/tmp/exemple.pdf");
		$actesArchivesSEDA->setActesFileName("exemple.pdf");
		$actesArchivesSEDA->setTransactionStatusInfo($actesTransactionsStatusInfo);
		
		$archive_path = $actesArchivesSEDA->getArchive();
		if (! $archive_path){
			$this->setLastMessage("Le test a échoué : " . $actesArchivesSEDA->getLastError()); 
			return false;
		}
		
		
		$bordereau = $actesArchivesSEDA->getBordereau($transactionsInfo);
		$donneesFormulaire->addFileFromData('sae_bordereau_test','bordereau.xml',$bordereau);
		$donneesFormulaire->setData("sae_archive_test",$archive_path);
		$this->setLastMessage("Le bordereau a été créé");
		return true;
	}
	
}