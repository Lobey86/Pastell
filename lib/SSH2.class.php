<?php
class SSH2 {
	
	private $server_name;
	private $server_fingerprint;
	private $login;
	private $password;
	
	private $lastError;
	
	public function setServerName($server_name,$server_fingerprint){
		$this->server_name = $server_name;
		$this->server_fingerprint = $server_fingerprint;
	}
	
	public function setPasswordAuthentication($login,$password){
		$this->login = $login;
		$this->password = $password;
	}

	public function getLastError(){
		return $this->lastError;
	}
	
	public function listDirectory($directory){
		$connexion = $this->getConnexion();
		if ( ! $connexion ){
			return false;
		}
		$sftp = ssh2_sftp($connexion);
		return scandir("ssh2.sftp://{$sftp}{$directory}");
	}
	
	public function getFileContent($path_on_server){
		$connexion = $this->getConnexion();
		if ( ! $connexion ){
			return false;
		}
		$sftp = ssh2_sftp($connexion);
		return @ file_get_contents("ssh2.sftp://{$sftp}{$path_on_server}");
	}
	
	public function retrieveFile($path_on_server,$local_path){
		$connexion = $this->getConnexion();
		if ( ! $connexion ){
			return false;
		}
		$result = ssh2_scp_recv($connexion,$path_on_server,$local_path);
		if (! $result){
			$this->lastError = "Impossible de copier (fichier distant) $path_on_server vers (fichier local) $local_path";
			return false;
		}
		
		
		return true;
	} 	
	
	
	private function getConnexion(){
		static $connexion;
		if ($connexion){
			return $connexion;
		}	
		assert('$this->server_name');
		assert('$this->server_fingerprint');
		
		@ $ssh_connexion = ssh2_connect($this->server_name);
		if (! $ssh_connexion ){
			$this->lastError = "Connexion au serveur SSH impossible";
			return false;
		}
		
		$server_fingerprint = ssh2_fingerprint($ssh_connexion);
		if ($server_fingerprint != $this->server_fingerprint){
			$this->lastError = "L'empreinte du serveur ($server_fingerprint) ne correspond pas à l'empreinte de la configuration ({$this->server_fingerprint})";
			return false;
		}
		
		if ( ! @ ssh2_auth_password($ssh_connexion,$this->login,$this->password)){
			$this->lastError = "Login ou mot de passe incorrect";
			return false;
		}
		
		return $ssh_connexion;
	}	
	
	public function deleteFile($filename){
		$connexion = $this->getConnexion();
		if ( ! $connexion ){
			return false;
		};
		$sftp = ssh2_sftp($connexion);
		if ( ! file_exists("ssh2.sftp://{$sftp}{$filename}")){
			return true;
		}
		
		return ssh2_sftp_unlink($sftp,$filename);
	}
	
	public function sendFile($file_path,$directory){
		$connexion = $this->getConnexion();
		if ( ! $connexion ){
			return false;
		};
		return ssh2_scp_send($connexion,$file_path,$directory . "/" .basename($file_path),0600);
	}
	
}