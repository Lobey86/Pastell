<?php
class FakeSAE extends Connecteur {
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		
	}
	
	public function sendArchive(){
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
}