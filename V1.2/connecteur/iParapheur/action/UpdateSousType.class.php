<?php 

class UpdateSousType extends ActionExecutor {
	
	public function go(){
		$signature = $this->getMyConnecteur();
		
		$properties = $this->getConnecteurProperties();
		$all_sous_type= $signature->getSousType();
		$content = "";
		foreach($all_sous_type as $sous_type){
			$content .="$sous_type\n";	
		}
		$properties->addFileFromData('iparapheur_sous_type','iparapheur_sous_type.txt',$content);
		$this->setLastMessage("Les sous-types ont été mis à jour");
		return true;
	}
	
}