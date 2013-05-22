<?php 

require_once(__DIR__ . "/../../connecteur-type/GEDConnecteur.class.php");

class GEDFTP extends GEDConnecteur {
	
	private $server;
	private $login;
	private $password;
	private $folder;
	
	function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->server = $donneesFormulaire->get('server');
		$this->login = $donneesFormulaire->get('login');
		$this->password = $donneesFormulaire->get('password');
		$this->folder = $donneesFormulaire->get('folder');
	}
	
	private function getConnection(){
		@ $conn_id = ftp_connect($this->server) or $this->returnError(); 
		@ ftp_login($conn_id, $this->login, $this->password) or  $this->returnError();
		return $conn_id;
	}
	
	public function createFolder($folder,$title,$description){
		$conn_id = $this->getConnection();
        if (in_array("$folder/$title", @ ftp_nlist($conn_id, $folder))) {
        	return;
        } 
		@ ftp_chdir($conn_id,$folder) or $this->returnError(); 
		@ ftp_mkdir($conn_id,$title) or  $this->returnError();
	}
	
	public function addDocument($title,$description,$contentType,$content,$gedFolder){
		$file_tmp = tempnam("/tmp","pastell_ged_ftp");
		file_put_contents($file_tmp, $content);
		$conn_id = $this->getConnection();
		ftp_put($conn_id,"$gedFolder/$title",$file_tmp,FTP_BINARY);
		unlink($file_tmp);
	}
	
	public function getRootFolder(){
		return $this->folder;
	}
	
	public function listFolder($folder){
		$conn_id = $this->getConnection();
		return ftp_nlist($conn_id,$this->folder);
	}
	
	public function returnError(){
		$last_error = error_get_last();
		throw new Exception($last_error['message']);
	}
	
}