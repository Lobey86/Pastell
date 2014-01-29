<?php 

class GEDFTP extends GEDConnecteur {
	
	private $server;
	private $login;
	private $password;
	private $passive_mode;
	private $folder;
	
	function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->server = $donneesFormulaire->get('server');
		$this->login = $donneesFormulaire->get('login');
		$this->password = $donneesFormulaire->get('password');
		$this->folder = $donneesFormulaire->get('folder');
		$this->passive_mode = $donneesFormulaire->get('passive_mode');
	}
	
	public function getSanitizeFolderName($folder){
		$folder = strtr($folder," àáâãäçèéêëìíîïñòóôõöùúûüýÿ","_aaaaaceeeeiiiinooooouuuuyy");
		$folder = preg_replace('/[^\w_]/',"",$folder);
		return $folder;		
	}
	
	private function getConnection(){
		static $conn_id;
		if ($conn_id){
			return $conn_id;
		}
		@ $conn_id = ftp_connect($this->server) or $this->returnError(); 
		@ ftp_login($conn_id, $this->login, $this->password) or  $this->returnError();
		ftp_pasv($conn_id,$this->passive_mode?true:false); 
		return $conn_id;
	}
	
	public function createFolder($folder,$title,$description){
		
		$folder_list = $this->listFolder($folder);
		if (in_array($title, $folder_list)) {
        	return;
        }
		
		$conn_id = $this->getConnection();
		
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
		$nlist = ftp_nlist($conn_id,$folder);
		if (!$nlist){
			return array();
		}
		//Attention, en fonction du serveur, les fichiers contiennent ou non le nom du répertoire !
		foreach($nlist as $file){
			$result[] = basename($file);
		}
		return $result;
	}
	
	public function returnError(){
		$last_error = error_get_last();
		throw new Exception($last_error['message']);
	}
	
}