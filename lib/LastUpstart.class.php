<?php
class LastUpstart {
	
	private $upstart_touch_file;
	private $upstart_time_send_warning;
	
	public function __construct($upstart_touch_file,$upstart_time_send_warning){
		$this->upstart_touch_file = $upstart_touch_file;
		$this->upstart_time_send_warning = $upstart_time_send_warning;
	}
	
	public function updatePID(){
		$info = $this->getInfo();
		$info['pid'] = getmypid();
		$this->saveInfo($info);
	}
	

	public function deletePID(){
		$info = $this->getInfo();
		$info['pid'] = false;
		$this->saveInfo($info);
	}
	
	private function saveInfo(array $info){
		file_put_contents($this->upstart_touch_file, serialize($info));
	}
	
	private function getInfo(){
		$content = file_get_contents($this->upstart_touch_file);
		if (! $content){
			return array('pid'=>false,'time'=>0);
		}
		return unserialize($content);
	}
	
	public function updateMtime(){		
		$info = $this->getInfo();
		$info['time'] = time();
		$this->saveInfo($info);
	}
	
	
	
	public function getLastMtime(){
		$info = $this->getInfo();
		if (! $info['time']) {
			return false;
		}
		return date("Y-m-d H:i:s",$info['time']);
	}
	
	public function hasWarning() {
		$info = $this->getInfo();
		if (! $info['time']) {
			return true;
		}
		return (time() - $info['time']) >= $this->upstart_time_send_warning;
	}
	
	public function getPID(){
		$info = $this->getInfo();
		return $info['pid'];
	}
	
	
}