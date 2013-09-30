<?php 
class SendAE extends ActionExecutor {
	
	public function go(){
		$message = "AE envoyé; dossier terminé";
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,"termine",$message);
		return true;
	}
	
}

