<?php
class YMLLoader {
	
	const CACHE_PREFIX = "yml_cache_"; 
	const CACHE_PREFIX_MD5 = "yml_cache_md5_";
	
	public function getArray($filename){
		ob_start();
		include($filename);
		$content = ob_get_clean();
		
		$md5 = md5($content);
		$md5_cache = apc_fetch( self::CACHE_PREFIX_MD5 . $filename );		
		if ($md5 == $md5_cache){
			return apc_fetch( self::CACHE_PREFIX . $filename);
		} 	
		
		$result = Spyc::YAMLLoadString($content);
		apc_store(self::CACHE_PREFIX . $filename,$result);
		apc_store(self::CACHE_PREFIX_MD5 .$filename,$md5);
		return $result;
	}
	
}