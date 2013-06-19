<?php 

class GEDFTPTestConnect extends ActionExecutor {
	
	public function go(){
		$ged = $this->getMyConnecteur();
		$my_folder = $ged->getRootFolder();
		$l = implode(",",$ged->listFolder($my_folder));
		$this->setLastMessage($l);
		return true;
	}
	
}