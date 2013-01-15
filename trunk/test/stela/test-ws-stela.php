<?php 

$options= 	array(
	'compression'=>true,
	'exceptions'=>true,
	'trace'=>true,
	'local_cert' => "./certificat.pem",
	'passphrase' => "1234",
	'encoding'=>'UTF-8'
);

$client = new SoapClient("https://demostela.sictiam.fr/ws-helios.wsdl", $options);

$authHeader["SSLCertificatSerial"]="1C7467D94C7E7145791392B0ECDBB3DF2D5F3678";
$authHeader["SSLCertificatVendor"]="/C=FR/ST=Alpes-Maritimes/L=Vallauris/O=SICTIAM/CN=Certificats SICTIAM/emailAddress=internet@sictiam.fr";		

$header=new SoapHeader("https://demostela.sictiam.fr/",'authHeader',$authHeader); 
$client->__setSoapHeaders($header);

$uid = 381;



$group_id = 11;
		
$file_path = "PESALR1_99988877766655_121211_dbab3859d03b59d5c67a94795da532cf.xml";
$file =  base64_encode(file_get_contents($file_path));

$helios_info = array();
$helios_info['fichier'][] = array(
                                    "base64"        => $file,
                                    "name"            => $file_path
                                );

$helios_info['informations']	= array(
	"uid"		=> $uid, 
	"groupsend"	=> 11,
	"title" => "TEST1310113",
	"comment" => "Test depot demostela via webservice",
);

                           


$result = $client->putPESAller(json_encode($helios_info));
$result = json_decode($result, true);
print_r($result);

