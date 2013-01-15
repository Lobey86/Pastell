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


$tableau = array();
$filename = "PESALR1_99988877766655_121211_08.xml";
//$filename = "PESALR2_99988877766655_150113_01.xml";
$cheminFichier = $filename;
$chaine_fichier = file_get_contents($cheminFichier);
$file = base64_encode($chaine_fichier);
$tableau['fichier'][]    = array(
                                    "base64"        => $file,
                                    "name"            => $filename
                                );
$tableau['informations']    = array(
                                        "uid"             => $uid,
                                        "groupsend"     => 11,
                                        "title"         => "TEST1310112",
                                        "comment"         => "Test depot demostela via webservice"
                                    );
                                   

print_r($tableau);                                    
                                    
$resultatTelecharge = $client->putPESAller( json_encode( $tableau ));   
$var = json_decode($resultatTelecharge, true);

print_r($var);

