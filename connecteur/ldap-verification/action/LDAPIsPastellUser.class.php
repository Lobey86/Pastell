<?php 

class LDAPIsPastellUser extends ActionExecutor {
	
	public function go(){
		$ldap = $this->getMyConnecteur();
		$users = $ldap->getUserToCreate($this->objectInstancier->Utilisateur);
		$result = "Déjà présent dans pastell : " . implode($users['allready'],",") ."<br/><br/>";
		if ($users['todo']){
			$result .= "A créer dans Pastell : <ul>";
			foreach($users['todo'] as $user) {
				$result .= "<li>{$user['login']}  ({$user['entite']})</li>";
			}
			$result .="</ul>";
		}
		$this->setLastMessage($result);
		return true;
	}
	
}