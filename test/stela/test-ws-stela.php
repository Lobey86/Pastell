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

		
$helios_info['informations']	= array(
	"uid"		=> $uid, 
	"groupSend"	=> $group_id,
	"title" => "test",
	"comment" => "Fichier Helios poste via Pastell",
);

$file_path = "./PESALR2_12345678912345_2013-01-11_01.xml";


$helios_info['fichier']['name'] = basename($file_path);
$helios_info['fichier']['base64'] = base64_encode(file_get_contents($file_path));

$result = $client->putPESAller(json_encode($helios_info));
$result = json_decode($result, true);
print_r($result);


