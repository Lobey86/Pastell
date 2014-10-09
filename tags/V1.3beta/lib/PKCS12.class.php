<?php 
class PKCS12 {
	
	public function getAll($p12_file_path,$p12_password){
		if (! file_exists($p12_file_path)){
			return false;
		}
		$pkcs12 = file_get_contents( $p12_file_path );
	
		$result = openssl_pkcs12_read( $pkcs12, $certs, $p12_password );
		
		if (! $result){			
			return false;
		}
		openssl_pkey_export($certs['pkey'],$pkey,$p12_password);
		return array('cert' => $certs['cert'],'pkey' => $pkey);
	}
	
}