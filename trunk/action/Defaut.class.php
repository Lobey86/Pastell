<?php


class Defaut extends ActionExecutor {

	public function go(){
		
		$infoDocument = $this->getDocument()->getInfo($this->id_d);
		$documentType = $this->getDocumentTypeFactory()->getDocumentType($infoDocument['type']);
		$theAction = $documentType->getAction();
		$actionName = $theAction->getActionName($this->action);
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"L'action $actionName a été executé sur le document");
		$this->setLastMessage("L'action $actionName a été executé sur le document");
		return true;
	}

}