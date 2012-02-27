<?php

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class IParapheurEnvoie extends ActionExecutor {
	
	public function go(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		
		$iParapheur = new IParapheur($collectiviteProperties);		
		$actes = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($actes->getFilePath('arrete'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($actes->getFilePath('arrete'),FILEINFO_MIME_TYPE);
		
		$annexe = array();
		if ($actes->get('autre_document_attache')) {
			foreach($actes->get('autre_document_attache') as $num => $fileName ){
				$annexe_content =  file_get_contents($actes->getFilePath('autre_document_attache',$num));
				$annexe_content_type = $finfo->file($actes->getFilePath('autre_document_attache',$num),FILEINFO_MIME_TYPE);
					
				$annexe[] = array(
					'name' => $fileName,
					'file_content' => $annexe_content,
					'content_type' => $annexe_content_type,
				);
				
			}
		}
		
		$dossierID = $iParapheur->getDossierID($actes->get('numero_de_lacte'),$actes->get('objet'));
		
		$result = $iParapheur->sendDocument($actes->get('iparapheur_type'),
											$actes->get('iparapheur_sous_type'),
											$dossierID,
											$file_content,
											$content_type,$annexe);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au parapheur électronique");			
		
		$this->setLastMessage("Le document a été envoyé au parapheur électronique");
		return true;			
	}
	
}