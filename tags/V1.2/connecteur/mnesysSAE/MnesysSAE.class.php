<?php 

class MnesysSAE extends SAEConnecteur {
	
	private $url;
	
	public function  setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		$this->url = $collectiviteProperties->get('url');
	}
	
		
	public function generateArchive($bordereau,$tmp_folder){
		$fileName = "/tmp/donnee.zip";
		$command = "zip -r $fileName $tmp_folder";
		$status = exec($command );
		if (! $status){
			$this->lastError = "Impossible de créer le fichier d'archive $fileName";
			return false;
		}
		return $fileName;
	}	
	
	public function sendArchive($bordereauSEDA,$archivePath,$file_type="TARGZ",$archive_file_name="archive.tar.gz"){
		file_put_contents("/tmp/bordereau_seda.xml", $bordereauSEDA);
		$curlWrapper = new CurlWrapper();
		$curlWrapper->addPostFile('name_xml', "/tmp/bordereau_seda.xml");
		$curlWrapper->addPostFile('name_zip', $archivePath);
		$result = $curlWrapper->get($this->url);
		if ($result == "000"){
			return "ok";
		}
		throw new Exception("Réponse de Mnesys non documenté");
		
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