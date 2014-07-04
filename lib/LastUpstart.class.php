<?php
class LastUpstart {
	
	private $upstart_touch_file;
	private $upstart_time_send_warning;
	
	public function __construct($upstart_touch_file,$upstart_time_send_warning){
		$this->upstart_touch_file = $upstart_touch_file;
		$this->upstart_time_send_warning = $upstart_time_send_warning;
	}
	
	public function updateMtime(){
		touch($this->upstart_touch_file);
	}
	
	public function getLastMtime(){
		if (file_exists($this->upstart_touch_file)) {
			return date("Y-m-d H:i:s",filemtime($this->upstart_touch_file));
		} else {
			return false;
		}
	}
	
	public function hasWarning() {
		if (! file_exists($this->upstart_touch_file)) {
			return true;
		}
		return (time() - filemtime($this->upstart_touch_file)) >= $this->upstart_time_send_warning;
	}
	
	
}