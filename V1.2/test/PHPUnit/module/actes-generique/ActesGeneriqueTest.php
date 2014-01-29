<?php

require_once __DIR__.'/../../init.php';

class ActesGeneriqueTest extends PastellTestCase {
	
	const FLUX_ID = "actes-generique";
	
	public function reinitDatabaseOnSetup(){
		return true;
	}
	
	public function reinitFileSystemOnSetup(){
		return true;
	}

	public function testValidation(){
		$apiAction = new APIAction($this->getObjectInstancier(), PastellTestCase::ID_U_ADMIN);
		$info = $apiAction->createDocument(PastellTestCase::ID_E_COL, self::FLUX_ID);
		$this->assertNotEmpty($info['id_d']);
		
		$id_d = $info['id_d'];
	
		$info['id_e'] = PastellTestCase::ID_E_COL;
		$info['acte_nature'] = 1;
		$info['numero_de_lacte'] = "TEST20131202A";
		$info['objet'] = "Test d'un actes soumis au contrôle de légalité";
		$info['date_de_lacte'] = "2013-12-02";
		$info['envoi_signature'] = 1;
		$info['envoi_tdt'] = 1;
		$info['envoi_sae'] = 1;
		$info['envoi_ged']  = 1;
		$info['classification'] =  '2.1 Documents d urbanisme';
		$info['iparapheur_type'] = 'Actes';
		$info['iparapheur_sous_type'] = 'Deliberation';
		
		$result = $apiAction->modifDocument($info);
		$this->assertEquals(APIAction::RESULT_OK, $result['result']);
		
		$result = $apiAction->sendFile(PastellTestCase::ID_E_COL, $info['id_d'], 'arrete', "vide.pdf",0, file_get_contents(FIXTURES_PATH)."/vide.pdf");
		$this->assertEquals(APIAction::RESULT_OK, $result['result']);
		
		$all_sous_type = $apiAction->externalData(PastellTestCase::ID_E_COL, $info['id_d'], 'iparapheur_sous_type');
		$this->assertArrayHasKey(0, $all_sous_type);
	
		foreach(array('send-iparapheur','verif-iparapheur',
					'send-tdt','verif-tdt',
					'send-archive','verif-sae','validation-sae',
					'send-ged'
					) as $action) {

			$result = $apiAction->detailDocument(PastellTestCase::ID_E_COL, $id_d);
			$this->assertTrue(in_array($action, $result['action-possible']),"Action $action n'est pas présente");
						
			$result = $apiAction->action(PastellTestCase::ID_E_COL,$info['id_d'],$action);
			if (empty($result['result'])) {
				print_r($result);
			}
			$this->assertEquals(1, $result['result'],"L'action $action a échoué");
		}
				
	}
}