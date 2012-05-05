<?php

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/lib/Array2XML.class.php");

class IParapheurRecupHelios extends ActionExecutor {
	
	public function go(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);		
		$helios = $this->getDonneesFormulaire();
		
		$file_array = $helios->get('fichier_pes');
		$filename = $file_array[0];
		
		$dossierID = $iParapheur->getDossierID($helios->get('objet'),$filename);
		
		$all_historique = $iParapheur->getAllHistoriqueInfo($dossierID);
		
		if (! $all_historique){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}
		
		$array2XML = new Array2XML();
		$historique_xml = $array2XML->getXML('iparapheur_historique',json_decode(json_encode($all_historique),true));
		
		
		$helios->setData('has_historique',true);
		$helios->addFileFromData('iparapheur_historique',"iparapheur_historique.xml",$historique_xml);
		
		$result = $iParapheur->getLastHistorique($all_historique);
		
		if (strstr($result,"[Archive]")){
			return $this->retrieveDossier();
		}
		if (strstr($result,"[RejetVisa]") || strstr($result,"[RejetSignataire]")){
			$this->rejeteDossier($result);
			$iParapheur->effacerDossierRejete($dossierID);
		}
		$this->setLastMessage($result);
		return true;			
	}
	
	public function rejeteDossier($result){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);
		$actes = $this->getDonneesFormulaire();
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'rejet-iparapheur',"Le document a été rejeté dans le parapheur : $result");
	}
	
	public function retrieveDossier(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);		
		$helios = $this->getDonneesFormulaire();
		$file_array = $helios->get('fichier_pes');
		$filename = $file_array[0];
		
		$dossierID = $iParapheur->getDossierID($helios->get('objet'),$filename);
		
		$info = $iParapheur->getSignature($dossierID);
		if (! $info ){
			$this->setLastMessage("La signature n'a pas pu être récupéré : " . $iParapheur->getLastError());
			return false;
		}
		
		$helios->setData('has_signature',true);
		if ($info['signature']){
			$helios->addFileFromData('fichier_pes_signe',$filename,$info['signature']);
		} else {
			$fichier_pes_path = $helios->getFilePath('fichier_pes',0);
			$fichier_pes_content = file_get_contents($fichier_pes_path);
			$helios->addFileFromData('fichier_pes_signe',$filename,$fichier_pes_content);
		}
		$helios->addFileFromData('document_signe',$info['nom_document'],$info['document']);
		
		$this->setLastMessage("La signature a été récupérée");
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'recu-iparapheur',"La signature a été récupérée sur parapheur électronique");			
		return true;
		
	} 
	
}