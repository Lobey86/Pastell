<?php
class AsalaeRestPing extends ActionExecutor {
	
	public function go() {
		$asalae = $this->getMyConnecteur();
		$message = $asalae->ping();
		$this->setLastMessage($message);
		return true;
	}
}