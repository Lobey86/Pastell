<?php
require_once __DIR__.'/../init.php';
class FournisseurControlerTest extends PastellTestCase {
	
	const FLUX_ID = 'fournisseur-invitation';
	
	public function __construct(){
		parent::__construct();
		$this->getFournisseurControler()->setDontRedirect(true);
	}
	
	/**
	 * @return ConnexionControler
	 */
	private function getFournisseurControler(){
		return $this->getObjectInstancier()->FournisseurControler;
	}
	
	public function reinitDatabaseOnSetup(){
		return true;
	}
	
	public function reinitFileSystemOnSetup(){
		return true;
	}
	
	/**
	 * @expectedException LastMessageException
	 */
	public function testDoInscription(){
		$apiAction = new APIAction($this->getObjectInstancier(), PastellTestCase::ID_U_ADMIN);
		$info = $apiAction->createDocument(PastellTestCase::ID_E_COL, self::FLUX_ID);
		
		$donneesFormulaire = $this->getObjectInstancier()->DonneesFormulaireFactory->get($info['id_d']);
		$donneesFormulaire->setData('_fournisseur_inscription_secret','42');		
		
		$_POST['id_e'] = PastellTestCase::ID_E_COL;
		$_POST['id_d'] = $info['id_d'];
		$_POST['s'] = "42";
		$_POST['login'] = "fournisseur1";
		$_POST['password'] = "fournisseur1";
		$_POST['password2'] = "fournisseur1";
		$_POST['siren'] = "957105125";
		$_POST['denomination'] = "fournisseur1";
		$_POST['email'] = "eric@sigmalis.com";
		
		$this->getFournisseurControler()->doInscriptionAction();
	}
	
}