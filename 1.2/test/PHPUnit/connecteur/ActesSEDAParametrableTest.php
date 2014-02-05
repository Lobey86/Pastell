<?php
require_once __DIR__.'/../init.php';

class ActesSEDAParametrableTest extends PastellTestCase  {

	public function reinitDatabaseOnSetup(){
		return true;
	}
	
	public function reinitFileSystemOnSetup(){
		return true;
	}

	/**
	 * @return DonnesFormulaire
	 */
	public function getDonneesFormulaire(){
		return $this->getObjectInstancier()->DonneesFormulaireFactory->getConnecteurEntiteFormulaire(8);
	}
	
	public function testAll(){
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$param_xml = PASTELL_PATH."/connecteur/actes-seda-parametrable/fixtures/bordereau-parametrable.xml";
		$donneesFormulaire->addFileFromData('seda_parametrable','param.xml',file_get_contents($param_xml));
		
		$result = $this->getObjectInstancier()->ActionExecutorFactory->executeOnConnecteur(8,PastellTestCase::ID_U_ADMIN,'test-bordereau', true);
		$this->assertTrue($result,$this->getObjectInstancier()->ActionExecutorFactory->getLastMessage());
	}
}