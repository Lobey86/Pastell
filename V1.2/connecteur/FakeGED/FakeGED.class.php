<?php

class FakeGED extends GEDConnecteur {
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){}
	public function createFolder($folder,$title,$description) {}
	public function addDocument($title,$description,$contentTpe,$content,$gedFolder){}
	public function getRootFolder(){}
	public function listFolder($folder){}
	public function getSanitizeFolderName($folder){}

}