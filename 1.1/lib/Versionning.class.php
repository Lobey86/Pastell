<?php
class Versionning {
	
	private $versionFile ;
	private $revisionFile;
	
	public function __construct($versionFile,$revisionFile){
		$this->versionFile = $versionFile;
		$this->revisionFile = $revisionFile;
	}
	
	public function getRevision(){
		$revisionFileContent = file_get_contents($this->revisionFile);
		foreach(explode("\n",$revisionFileContent) as $line){
			if (preg_match('#^\$Rev: (\d*) \$#',$line,$matches)){
				return $matches[1];
			}
		}
		return false;
	}
	
	public function getVersion(){
		return file_get_contents($this->versionFile);
	}
	
	public function getAllInfo(){
		$result = array('version' => $this->getVersion(),'revision'=>$this->getRevision());
		$result['version-complete'] =  "Version {$result['version']} - Révision  {$result['revision']}" ;
		return $result;
	}
}