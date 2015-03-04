<?php
class EnvoieSignatureChange extends ActionExecutor{
	
	public function go(){	
		
		if ($this->getDonneesFormulaire()->get('envoi_signature_check')){
			$localSignature = $this->getConnecteur('signature')->isLocalSignature();
			$this->getDonneesFormulaire()->setData('envoi_signature', ! $localSignature);
			$this->getDonneesFormulaire()->setData('signature_locale_display', $localSignature);
		} else {
			$this->getDonneesFormulaire()->setData('envoi_signature', false);
			$this->getDonneesFormulaire()->setData('signature_locale_display', false);
			return;
		}
		
		$recuperateur = new Recuperateur($_POST);		
		if ( $recuperateur->get('suivant') || $recuperateur->get('precedent')){
			return;
		}		
		
		$page = $this->getFormulaire()->getTabNumber($localSignature?"Signature locale":"Parapheur");
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=$page");
	}
}