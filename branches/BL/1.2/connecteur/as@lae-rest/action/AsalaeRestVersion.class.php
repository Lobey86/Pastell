<?php
class AsalaeRestVersion extends ActionExecutor {
	
	public function go(){
		$asalae = $this->getMyConnecteur();
		$message = $asalae->getVersion();
		$this->setLastMessage(json_encode($message));
		return true;
	}
	
}