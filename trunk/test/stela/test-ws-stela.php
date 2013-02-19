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

$result = $client->getDetailsPESAller(577);

$result = json_decode($result, true);
print_r($result);

