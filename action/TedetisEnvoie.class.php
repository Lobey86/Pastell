<?php
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");



class TedetisEnvoie  extends ActionExecutor {

	public function go(){
		$collectviteProperties = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
		
		$tedetis = new Tedetis($collectviteProperties);
		
		
		if (!  $tedetis->postActes($donneesFormulaire) ){
			$lastError->setLastError( $tedetis->getLastError());
			header("Location: detail.php?id_d=$id_d&id_e=$id_e");
			exit;
		}
		
		
		$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
		$actionCreator->addAction($id_e,$authentification->getId(),$action,"Le document a été envoyé au contrôle de légalité");
			
		
		$lastMessage->setLastMessage("Le document a été envoyé au contrôle de légalité");
			
		header("Location: detail.php?id_d=$id_d&id_e=$id_e");
	}
}