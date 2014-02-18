<?php 

require_once __DIR__.'/../../init.php';

class MessageServiceTest extends PastellTestCase {

	const FLUX_ID = "message-service";
	
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
		
		$info['id_e'] = PastellTestCase::ID_E_COL;
		$info['objet'] = "Test de message de service";
		$info['message'] = "Ceci est un message de service (test)";		
		$result = $apiAction->modifDocument($info);
		$this->assertEquals(APIAction::RESULT_OK, $result['result']);
		
		$result = $apiAction->action(PastellTestCase::ID_E_COL, $info['id_d'], 'envoi',array(PastellTestCase::ID_E_SERVICE));
		$this->assertEquals(1, $result['result']);
		
		$document_list = $apiAction->listDocument(PastellTestCase::ID_E_SERVICE, self::FLUX_ID, 0, 10);
		$this->assertEquals(1, count($document_list));
		
		$result = $apiAction->action(PastellTestCase::ID_E_SERVICE, $info['id_d'], 'accuse_de_reception');
		$this->assertEquals(1, $result['result']);
		
		$detail = $apiAction->detailDocument(PastellTestCase::ID_E_SERVICE, $info['id_d']);
		$this->assertEquals($info['message'], $detail['data']['message']);
		$this->assertEquals($info['objet'], $detail['data']['objet']);

		$detail = $apiAction->detailDocument(PastellTestCase::ID_E_COL, $info['id_d']);
		$this->assertEquals('accuse_de_reception',$detail['last_action']['action']);
	}
	
}