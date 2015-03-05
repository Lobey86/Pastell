<?php
class TmpFile {

	private $temp_dir;
	
	public function __construct($temp_directory = false){
		if (!$temp_directory){
			$temp_directory = sys_get_temp_dir();
		}
		$this->temp_dir = $temp_directory;
	}
	
	public function create(){
		$file_name = $this->temp_dir . "/pastell_tmp_file_" . mt_rand(0,mt_getrandmax());
		if (file_exists($file_name)) {
			throw new Exception("Impossible de créer un fichier temporaire : le fichier $file_name existe");
		}
		return $file_name;
	}
	
	public function delete($filename){
		if (! file_exists($filename)) {
			return;
		}
		unlink($filename);
		
	}
	
}