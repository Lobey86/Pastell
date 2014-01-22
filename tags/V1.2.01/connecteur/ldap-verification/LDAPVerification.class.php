<?php 

class LDAPVerification extends Connecteur {
	
	private $ldap_host;
	private $ldap_port;
	private $ldap_user;
	private $ldap_password;
	private $ldap_filter;
	private $ldap_dn;
	private $ldap_root;
	
	function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		foreach(array(	'ldap_host',
						'ldap_port',
						'ldap_user',
						'ldap_password',
						'ldap_filter',
						'ldap_dn',
						'ldap_root',
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
		$dn = $this->ldap_root;
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
		
	public function getUserToCreate(Utilisateur $utilisateur){
		$entries = $this->getAllUser();
		unset($entries['count']);
		$result = array();
		foreach($entries as $entry){
			$login = $this->getLogin($entry['dn']);
			if (!$login){
				continue;
			}
			$email = isset($entry['mail'])?$entry['mail'][0]:"";
			$prenom = isset($entry['givenname'])?$entry['givenname'][0]:"";
			$nom = $entry['sn'][0];
			
			$ldap_info = array('login'=>$login,'prenom'=>$prenom,'nom'=>$nom,'email'=>$email);
			$id_u = $utilisateur->getIdFromLogin($login); 
			if (! $id_u){
				$ldap_info['create'] = true;
				$ldap_info['synchronize'] = true;
			} else {
				$ldap_info['create'] = false;
				$info = $utilisateur->getInfo($id_u);
				$ldap_info['id_u'] = $info['id_u'];
				$ldap_info['synchronize'] = $info['prenom'] != $prenom || $info['nom'] != $nom || $info['email'] != $email;
			}
			$result[] = $ldap_info;
		}
		return $result;
	}
	
}