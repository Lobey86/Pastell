<?php 

$options= 	array(
	'compression'=>true,
	'exceptions'=>true,
	'trace'=>true,
	'local_cert' => "./certificat.pem",
	'passphrase' => "1234",
	'encoding'=>'UTF-8'
);


$client = new SoapClient("https://demostela.sictiam.fr/ws-miat.wsdl", $options);
//$client = new SoapClient("https://demostela.sictiam.fr/ws-helios.wsdl", $options);


$authHeader["SSLCertificatSerial"]="1C7467D94C7E7145791392B0ECDBB3DF2D5F3678";
$authHeader["SSLCertificatVendor"]="/C=FR/ST=Alpes-Maritimes/L=Vallauris/O=SICTIAM/CN=Certificats SICTIAM/emailAddress=internet@sictiam.fr";		

$header=new SoapHeader("https://demostela.sictiam.fr/",'authHeader',$authHeader); 
$client->__setSoapHeaders($header);
//$connexionSTELA = $client->connexionSTELA('uid');

$connexionSTELA	= $client->annulationActe("057-999888777-20121001-TEST20121105A-AI");



$result = json_decode($connexionSTELA, true);
print_r($result);



/*
$acte_info['fichier'][]= array(
	"base64"=> base64_encode(file_get_contents("/home/eric/Bureau/vide.pdf")),
	"name" => 'vide.pdf'
);
$acte_info['informations']	= array(
		"uid"		=> "381", 
		"groupSend"	=> "11",
		"dateDecision"	=> "2012/10/01", 
		"numInterne"	=> "TEST20121105A", 
		"natureActe"	=> "3", 
		"matiereActe"	=> "1-1-0-0-0", 
		"objet"		=> "Test 2012 11 05 A",
		"numInterneOld"	=> ""
	);


$connexionSTELA	= $client->putActe(json_encode($acte_info));
$result = json_decode($connexionSTELA, true);
print_r($result);

$connexionSTELA	= $client->getDocument("057-999888777-20121001-TEST20121105A-AI",1);
$result = json_decode($connexionSTELA, true);
print_r($result);
*/

