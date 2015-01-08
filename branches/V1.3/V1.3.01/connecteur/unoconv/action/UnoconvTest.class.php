<?php
class UnoconvTest extends ActionExecutor {
	
	public function go(){
		$unoconv = $this->getMyConnecteur();
		$unoconv->convertField($this->getConnecteurProperties(),'document_test','document_test_result');
		$this->setLastMessage("Le document a été converti en PDF");
		return true;
	}
	
}