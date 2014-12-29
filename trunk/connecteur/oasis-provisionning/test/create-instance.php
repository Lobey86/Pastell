<?php


$url = "https://kernel.ozwillo-preprod.eu/apps/pending-instance/904b7ca1-9336-4168-9d34-1c0ed3aa0d72";
//$url = "http://www.yahoo.fr/";
$client_id = "904b7ca1-9336-4168-9d34-1c0ed3aa0d72";
$client_secret = "xFQbU88Rx6b1aeXX6roVSHtKqdHBsv+RDUJZAu3/iBQ";

$data = array("services"=>array('xx')); 

//'{"instance_id":"904b7ca1-9336-4168-9d34-1c0ed3aa0d72","client_id":"904b7ca1-9336-4168-9d34-1c0ed3aa0d72","client_secret":"xFQbU88Rx6b1aeXX6roVSHtKqdHBsv+RDUJZAu3/iBQ","user_id":"447e1478-461b-4802-b3a5-81fb7ae912c2","user":{"id":"447e1478-461b-4802-b3a5-81fb7ae912c2","name":"iguillaumedenis@yahoo.fr","email_address":"iguillaumedenis@yahoo.fr"},"organization_id":"4725bf47-8846-4fdb-adcf-542d373e0676","organization_name":"Sigmalis","organization":{"id":"4725bf47-8846-4fdb-adcf-542d373e0676","name":"Sigmalis","type":"PUBLIC_BODY"},"instance_registration_uri":"https://kernel.ozwillo-preprod.eu/apps/pending-instance/904b7ca1-9336-4168-9d34-1c0ed3aa0d72"}';


$ch = curl_init();

curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST,true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
curl_setopt($ch, CURLOPT_HEADER, 1);


$curlHttpHeader[] = "Content-Type: application/json";

curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHttpHeader);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

$output = curl_exec($ch);

print_r(curl_getinfo($ch,CURLINFO_HEADER_OUT));

//echo curl_error($ch);;

print_r($output);
