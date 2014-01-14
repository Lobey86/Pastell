<?php
class FakeSAE extends SAEConnecteur {
	private $tmpFile;
	
	private $collectiviteProperties;
	
	public function __construct(TmpFile $tmpFile){
		$this->tmpFile = $tmpFile;
	}
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		$this->collectiviteProperties = $collectiviteProperties;
	}
	
	public function sendArchive($bordereauSEDA,$archivePath,$file_type="TARGZ",$archive_file_name="archive.tar.gz"){
		$this->collectiviteProperties->addFileFromData('last_bordereau', 'bordereau_seda.xml', $bordereauSEDA);
		$this->collectiviteProperties->addFileFromData('last_file', 'donnes.zip', file_get_contents($archivePath));
		return true;
	}
	
	public function getAcuseReception($id_transfert){
		return "<test/>";
	}	
	
	
	public function getReply($id_transfer){
		return "<ArchiveTransferAcceptance><Archive><ArchivalAgencyArchiveIdentifier>http://www.google.fr</ArchivalAgencyArchiveIdentifier></Archive></ArchiveTransferAcceptance>";
	}
	
	public function getURL($cote){
		return "http://www.google.fr";
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
	
	public function getErrorString($number){
		
	}
	
}