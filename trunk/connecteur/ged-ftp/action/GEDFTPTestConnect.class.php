<?php 

class GEDFTPTestConnect extends ActionExecutor {
	
	public function go(){
		$ged = $this->getMyConnecteur();
		if ( ! $ged){
			$this->setLastMessage("Impossible de se connecter au serveur FTP");
			return false;
		}
		
		$my_folder = $ged->getRootFolder();
		$folder_list = $ged->listFolder($my_folder);
		if (!$folder_list){
			$this->setLastMessage("Impossible de lister le répertoire");
			return false;
		}
		$l = implode(",",$folder_list);
		$this->setLastMessage($l);
		return true;
	}
	
}