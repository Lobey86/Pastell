<?php
require_once( PASTELL_PATH . "/module/actes/lib/ActesArchiveSEDA.class.php");
class Megalis extends Connecteur {
	
	private $collectiviteProperties;
	private $ssh2;
	
	public function __construct(DonneesFormulaire $collectiviteProperties, SSH2 $ssh2){
		$this->collectiviteProperties = $collectiviteProperties;		
		$this->ssh2 = $ssh2;
	}
	
	private function getProperties($name){
		return $this->collectiviteProperties->get($name);
	}
	
	private function configSSH2(){
		$this->ssh2->setServerName($this->getProperties('emegalis_ssh_server'),$this->getProperties('emegalis_ssh_fingerprint'));
		$this->ssh2->setPasswordAuthentication($this->getProperties('emegalis_ssh_login'),$this->getProperties('emegalis_ssh_password'));
	}
	
	public function listDirectory(){
		$this->configSSH2();
		$directory_listing = $this->ssh2->listDirectory($this->getProperties("emegalis_ssh_directory"));
		if (!$directory_listing){
			$this->lastError = $this->ssh2->getLastError();
			return false;
		}
		return $directory_listing;
	}
	
	public function recup(){
		$directory_listing = $this->listDirectory();
		if (!$directory_listing){
			return false;
		}
		
		$file_ok = array();
		$file_ignored = array(); 
		
		foreach($directory_listing as $file_name){
			if (in_array($file_name,array('.','..'))){
				continue;
			}
			if ($this->isValidEmpreinteFileName($file_name)){
				continue;
			}
			if (! $this->isValidFileName($file_name)){
				$file_ignored[] = $file_name;
				continue;
			}
			$file_ok[] = $file_name;
		}
		return array('file_ok' => $file_ok,'file_ignored' => $file_ignored);
	}
	
	public function retrieveFile($file_name,$destination){
		$this->configSSH2();
		if (! $this->ssh2->retrieveFile($this->getProperties("emegalis_ssh_directory") . "/" . $file_name,$destination)){
			$this->lastError = $this->ssh2->getLastError();
			return false;
		}
		$md5_content = $this->ssh2->getFileContent($this->getProperties("emegalis_ssh_directory") . "/" .$file_name.".md5");
		if (!$md5_content){
			$this->lastError = "Impossible de trouver le fichier d'empreinte $file_name.md5";
			return false;
		}
		$md5_calcule = md5_file($destination);
		if ($md5_calcule != $md5_content){
			$this->lastError = "L'empreinte calculé du fichier ($md5_calcule) diffère de l'empreinte reçu ($md5_content)";
			return false; 
		}
		
		return $md5_content;
	}
	
	public function getSiren($file_name){
		return substr($file_name,0,9);
	}
	
	
	private function isValidEmpreinteFileName($file_name){
		return preg_match("#^\d{9}.*\.zip.md5#",$file_name);
	}
	
	private function isValidFileName($file_name){
		return preg_match("#^\d{9}.*\.zip#",$file_name);
	}
	
	public function delete($filename){
		$this->configSSH2();
		$d1 = $this->ssh2->deleteFile($this->getProperties("emegalis_ssh_directory") . "/" . $filename);
		$d2 = $this->ssh2->deleteFile($this->getProperties("emegalis_ssh_directory") . "/" . $filename.".md5");
		return $d1 && $d2;
	}
	
	public function createDepot(array $authorityInfo){
		$passwordGenerator = new PasswordGenerator();
		$tmp_dir = $passwordGenerator->getPassword();
		$tmp_dir_path = "/tmp/$tmp_dir/";
		mkdir($tmp_dir_path);
		copy(__DIR__."/../../../data-exemple/exemple.pdf","$tmp_dir_path/exemple.pdf");
		
		$uniq_id = time();
		$actesTransactionsStatusInfo = array(
			'transaction_id' => $uniq_id,
			'flux_retour' => '<empty></empty>',
			'date' => date("Y-m-d")
		);
		
		$transactionsInfo = array(
			'unique_id' => $uniq_id,
			'subject' => "bordereau de test",
			'decision_date' => '2012-02-18',
			'nature_descr' => 'Arretes individuels',
			'nature_code' => '1',
			'classification' => '1.1',
		);			
		$actesArchivesSEDA = new ActesArchiveSEDA($tmp_dir_path);
		$actesArchivesSEDA->setAuthorityInfo($authorityInfo);
		$actesArchivesSEDA->setActesFileName("exemple.pdf");
		$actesArchivesSEDA->setTransactionStatusInfo($actesTransactionsStatusInfo);
		$bordereau = $actesArchivesSEDA->getBordereau($transactionsInfo);
		
		file_put_contents($tmp_dir_path."/archive.xml", $bordereau);
		
		$zip_path = "/tmp/{$authorityInfo['siren']}_$uniq_id.zip";
		
		$phar = new PharData($zip_path);
		$phar->buildFromDirectory($tmp_dir_path);
		
		$this->configSSH2();
		$result = $this->ssh2->sendFile($zip_path,$this->getProperties("emegalis_ssh_directory"));
		
		$md5_path = $zip_path.".md5";
		file_put_contents($md5_path,md5_file($zip_path));
		$this->ssh2->sendFile($md5_path,$this->getProperties("emegalis_ssh_directory"));
		
		rrmdir($tmp_dir_path);
		unlink($zip_path);
		if ($result){
			return "{$authorityInfo['siren']}_$uniq_id.zip";
		}
		return false;
	}
	
}