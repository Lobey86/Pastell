<?php
class ActesSignatureLocale extends ActionExecutor {
	
	public function go(){
		$this->getDonneesFormulaire()->setData('signature_locale_display', true);
		$page = $this->getFormulaire()->getTabNumber("Signature locale");
		
		$this->addActionOK("Le document peut être signé");
		
		$this->setLastMessage("Vous pouvez signer le document");
		
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=$page");
		return true;
	}
	
}