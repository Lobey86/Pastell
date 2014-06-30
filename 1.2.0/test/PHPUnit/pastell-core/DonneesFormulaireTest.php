<?php

require_once __DIR__.'/../init.php';


class DonneesFormulaireTest extends PastellTestCase {

	public function reinitFileSystemOnSetup(){
		return true;
	}
	
	public function reinitDatabaseOnSetup(){
		return true;
	}

	/**
	 * @return DonneesFormulaire
	 */
	private function getDonneesFormulaire(){
		return $this->getObjectInstancier()->DonneesFormulaireFactory->get('toto','test');
	}
	
	/**
	 * @dataProvider getPassword
	 */
	public function testPassword($password){
		$recuperateur = new Recuperateur(array('password'=>$password));
		$this->getDonneesFormulaire()->saveTab($recuperateur, new FileUploader(), 0);
		$this->assertEquals($password,$this->getDonneesFormulaire()->get('password'));
	}
	
	public function getPassword(){
		return array(
				array('215900689B')
		);
	}

}