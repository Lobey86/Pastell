<?php 

abstract class GEDConnecteur extends Connecteur {
	abstract public function createFolder($folder,$title,$description);
	abstract public function addDocument($title,$description,$contentType,$content,$gedFolder);
	abstract public function getRootFolder();
	abstract public function listFolder($folder);
	
}