<?php

require_once(__DIR__."/../lib/HeliosSignature.class.php");

class HeliosGeneriqueSignatureLocale extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		$signature_1 = $recuperateur->get("signature_1");
		$isbordereau = $recuperateur->get("is_bordereau");
		if (! $signature_1){
			throw new Exception("Aucune signature n'a été trouvée");
		}
		
		$pes_filepath = $this->getDonneesFormulaire()->getFilePath("fichier_pes");
		$pes_filename  = $this->getDonneesFormulaire()->getFileName("fichier_pes");
		
		$heliosSignature = new HeliosSignature();
		
		$new_pes_content = $heliosSignature->injectSignature($pes_filepath, $signature_1, $isbordereau);
		
		$this->getDonneesFormulaire()->addFileFromData('fichier_pes', $pes_filename, $new_pes_content);
		$this->getDonneesFormulaire()->setData('signature_locale','OUI');
		
        
        $this->getActionCreator()->addAction($this->id_e,$this->id_u,'recu-iparapheur',"La signature a été récupérée depuis l'applet de signature");
		$this->notify('recu-iparapheur', $this->type,"La signature a été récupérée depuis l'aplet de signature");
        
		$this->setLastMessage("La signature a été correctement récupérée");
//		$this->notify('signature', $this->type,"La signature a été récupérée sur parapheur électronique");
		$this->redirect("/document/detail.php?id_e=".$this->id_e."&id_d=".$this->id_d."&page=".$this->page);
	}

	public function displayAPI(){
		throw new Exception("Cette fonctionnalité n'est pas disponible via l'API.");
	}
	
	public function display(){
		$this->libersign_properties =  $this->getConnecteurConfigByType('signature');
		
		$this->libersign_url = $this->getConnecteurConfigByType('signature')->get("libersign_applet_url");
		
		$pes_filepath = $this->getDonneesFormulaire()->getFilePath("fichier_pes");
		
		$heliosSignature = new HeliosSignature();
		$this->signatureInfo=$heliosSignature->getInfoForSignature($pes_filepath);
		
		$document_info = $this->getDocument()->getInfo($this->id_d);
		$this->info = $document_info;
		
		$type_name = $this->getDocumentTypeFactory()->getFluxDocumentType($this->type)->getName();
		
		$this->renderPage("Signature du fichier PES - " .  $document_info['titre'] . " (".$type_name.")","HeliosSignatureLocale");
	}
	
}