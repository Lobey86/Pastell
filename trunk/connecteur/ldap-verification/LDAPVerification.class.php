<?php 

class LDAPVerification extends Connecteur {
	
	private $ldap_host;
	private $ldap_port;
	private $ldap_user;
	private $ldap_password;
	private $ldap_filter;
	private $ldap_dn;
	private $ldap_attribute_entite;
	private $ldap_regexp_entite;
	private $ldap_exclude_entite;
	
	function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		foreach(array(	'ldap_host',
						'ldap_port',
						'ldap_user',
						'ldap_password',
						'ldap_filter',
						'ldap_dn',
						'ldap_attribute_entite',
						'ldap_regexp_entite',
						'ldap_exclude_entite',
				) as $variable){
			$this->$variable = $donneesFormulaire->get($variable);
		}
	}
	
	public function getConnexion(){
		$ldap = ldap_connect($this->ldap_host,$this->ldap_port);
		if (!$ldap){
			throw new Exception("Impossible de se connecter sur le serveur LDAP : " . ldap_error($ldap));
		}
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		if (! @ ldap_bind($ldap,$this->ldap_user,$this->ldap_password)){
			throw new Exception("Impossible de s'authentifier sur le serveur LDAP : ".ldap_error($ldap));
		}
		return $ldap;
	}
	
	public function getLogin($dn){
		$regexp = preg_replace("#%LOGIN%#","([^,]*)",$this->ldap_dn);
		preg_match("#$regexp#",$dn,$matches);
		if(isset($matches[1])){
			return $matches[1];
		}
		return false;
	}
	
	public function getEntry($user_id){
		$ldap = $this->getConnexion();
		$dn = preg_replace("#%LOGIN%#",$user_id,$this->ldap_dn);
		$filter = $this->ldap_filter;
		if (!$filter){
			$filter = "(objectClass=*)";
		}
		$result = @ ldap_read($ldap,$dn,$filter);
		if (! $result || ldap_count_entries($ldap,$result) < 1){
			return array();
		}
		$entries = ldap_get_entries($ldap,$result);
		return $entries[0];
	}
	
	public function getAllUser(){
		$ldap = $this->getConnexion();
		$dn = "";
		$filter = $this->ldap_filter;
		if (!$filter){
			$filter = "(objectClass=*)";
		}
		$result = @ ldap_search($ldap,$dn,$filter);
		if (! $result || ldap_count_entries($ldap,$result) < 1){
			return array();
		}
		$entries = ldap_get_entries($ldap,$result);
		return $entries;
	}
	
	public function getEntiteFromEntry($entry){
		if (empty($entry[$this->ldap_attribute_entite])){
			return false;
		}
		
		$regexp = preg_replace("#%ENTITE_NAME%#","([^,]*)",$this->ldap_regexp_entite);
		for($i=0; $i<$entry[$this->ldap_attribute_entite]['count']; $i++){
			$dn = $entry[$this->ldap_attribute_entite][$i];
			preg_match("#$regexp#",$dn,$matches);
			if (empty($matches[1])){
				continue;
			}
			if ($matches[1] == $this->ldap_exclude_entite){
				continue;
			}
			return $matches[1];
		}
		return false;
	}
	
	public function getEntite($user_id){
		$entry = $this->getEntry($user_id);
		return $this->getEntiteFromEntry($entry);
	}
	
	public function getUserToCreate(Utilisateur $utilisateur){
		$entries = $this->getAllUser();
		unset($entries['count']);
		$allready = array();
		$todo = array();
		foreach($entries as $entry){
			$login = $this->getLogin($entry['dn']);
			if (!$login){
				continue;
			}
			if ($utilisateur->getIdFromLogin($login)){
				$allready[] = $login;
			} else {
				$entite = $this->getEntiteFromEntry($entry)?:"entité racine";
				$email = isset($entry['mail'])?$entry['mail'][0]:"";
				$prenom = isset($entry['givenname'])?$entry['givenname'][0]:"";
				$todo[] = array('login'=>$login,'entite'=>$entite,'prenom'=>$prenom,'nom'=>$entry['sn'][0],'email'=>$email);
			}
		}
		return array('allready'=>$allready,'todo'=>$todo);
	}
	
}