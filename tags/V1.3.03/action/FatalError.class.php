<?php
class FatalError extends ActionExecutor {
	
	public function go(){
		$actionName  = $this->getActionName();
		$message = "Le document est en erreur fatale.";
		$this->objectInstancier->ActionAutoLogSQL->delete($this->id_e,$this->id_d);
		$this->addActionOK($message);
		$this->notify(ActionPossible::FATAL_ERROR_ACTION, $this->type, $message);
		return true;
	}
}