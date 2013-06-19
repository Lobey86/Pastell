<?php 

class SAEEnvoiActes extends ActionExecutor {
	
	public function go(){
		$tmp_folder = $this->objectInstancier->TmpFolder->create();
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$arrete = $donneesFormulaire->copyFile('arrete',$tmp_folder);
		
		$ar_actes = $donneesFormulaire->copyFile('aractes',$tmp_folder);
		$annexe = $donneesFormulaire->copyAllFiles('annexe',$tmp_folder);

		$acte_nature = $this->getFormulaire()->getField('acte_nature')->getSelect();
		
		$transactionsInfo = array(
			'numero_acte_collectivite' => $donneesFormulaire->get('numero_de_lacte'),
			'subject' => $donneesFormulaire->get('objet'),
			'decision_date' =>  $donneesFormulaire->get("date_de_lacte"),
			'latest_date' => $donneesFormulaire->get("date_de_lacte"),
			'nature_descr' => $acte_nature[$donneesFormulaire->get('acte_nature')],
			'nature_code' => $donneesFormulaire->get('acte_nature'),
			'classification' => $donneesFormulaire->get('classification'),
			'actes_file' => $arrete,
			'ar_actes' => $ar_actes,
			'annexe' => $annexe,
		);
		
		
		$actesSEDA = $this->getConnecteur('Bordereau SEDA');
		$bordereau = $actesSEDA->getBordereau($transactionsInfo);
		
		$sae = $this->getConnecteur('SAE');
		$archive_path = $sae->generateArchive($bordereau,$tmp_folder);
		
		$result = $sae->sendArchive($bordereau,$archive_path);
		$this->objectInstancier->TmpFolder->delete($tmp_folder);
		
		if (! $result){
			$this->setLastMessage("L'envoi du bordereau a échoué : " . $sae->getLastError());
			return false;
		} 
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au SAE");
		
		$this->setLastMessage("La transaction à été envoyé au SAE ");
		return true;
	}
} 