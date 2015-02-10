<?php
class TdtRetourTeletransmettre extends ActionExecutor {

	public function go(){
		$recuperateur = new Recuperateur($_GET);
		$error = $recuperateur->get("error");
		$message = $recuperateur->get("message");
		if ($error){
			throw new Exception("Erreur sur le Tdt : " . $message);
		}
		
		$this->changeAction("send-tdt", "Le document à été télétransmis à la préfecture");
		$this->notify('send-tdt', $this->type,"Le document à été télétransmis à la préfecture");
		
		return true;
	}
}