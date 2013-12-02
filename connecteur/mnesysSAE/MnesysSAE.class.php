<?php 

class MnesysSAE extends SAEConnecteur {
	
	private $url;
	private $tmpFile;
	
	public function __construct(TmpFile $tmpFile){
		$this->tmpFile = $tmpFile;
	}
	
	public function  setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		$this->url = $collectiviteProperties->get('url');
	}
	
	public function generateArchive($bordereau,$tmp_folder){
		$fileName = $this->tmpFile->create().".zip";
		
		$zip = new ZipArchive;
		
		if (! $zip->open($fileName,ZIPARCHIVE::CREATE)) {
			throw new Exception("Impossible de créer le fichier d'archive : $fileName");
		}
		foreach(scandir($tmp_folder) as $fileToAdd) {
			if (is_file("$tmp_folder/$fileToAdd")) {
				$zip->addFile("$tmp_folder/$fileToAdd", $fileToAdd);
			}
		}
		$zip->close();
		return $fileName;
	}	
	
	public function sendArchive($bordereauSEDA,$archivePath,$file_type="TARGZ",$archive_file_name="archive.tar.gz"){
		$bordereauPath = $this->tmpFile->create();
		file_put_contents($bordereauPath, $bordereauSEDA);
		$curlWrapper = new CurlWrapper();
		$curlWrapper->dontVerifySSLCACert();
		$curlWrapper->addPostData('name_xml', "bordereau_seda.xml");
		$curlWrapper->addPostFile('name_xml', $bordereauPath, "bordereau_seda.xml","text/xml","text");
		$curlWrapper->addPostData('name_zip', "donnees.zip");
		$curlWrapper->addPostFile('name_zip', $archivePath,"donnees.zip","application/zip","binary");
		
		$result = $curlWrapper->get($this->url);
		if (! $result){
			throw new Exception($curlWrapper->getLastError());
		}
		if ($result == "000"){
			return "ok";
		}
		$this->tmpFile->delete($archivePath);
		$this->tmpFile->delete($bordereauPath);
		throw new Exception("Réponse de Mnesys non documenté : $result");
	}
	
	public function getAcuseReception($id_transfert){
		throw new Exception("Not implemented");
	}
	
	public function getReply($id_transfer){
		throw new Exception("Not implemented");
	}
	
	public function getURL($cote){
		throw new Exception("Not implemented");
	}
	
	public function getErrorString($number){
		throw new Exception("Not implemented");
	}
	
	
}