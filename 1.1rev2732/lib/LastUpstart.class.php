<?php
class LastUpstart {
	
	private $upstart_touch_file;
	
	public function __construct($upstart_touch_file){
		$this->upstart_touch_file = $upstart_touch_file;
	}
	
	public function updateMtime(){
		touch($this->upstart_touch_file);
	}
	
	public function getLastMtime(){
		if (file_exists($this->upstart_touch_file)) {
			return date("Y-m-d H:i:s",filemtime(UPSTART_TOUCH_FILE));
		} else {
			return "JAMAIS !";
		}
	}
	
	
}