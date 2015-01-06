<?php



$url = "http://192.168.1.28:8888/adullact/pastell/web/oasis/cancel.php";

$data = '{"instance_id":"f217042c-bc7e-4066-a627-a4d44d0096f3"}';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST,true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
curl_setopt($ch, CURLOPT_HEADER, 1);


$curlHttpHeader[] = "Content-Type: application/json";
$curlHttpHeader[] = "X-Hub-Signature: sha1=891A90ADE38B1A20D5F139DDBF9AA4AC8BD54DA8";


curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHttpHeader);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

$output = curl_exec($ch);

print_r(curl_getinfo($ch,CURLINFO_HEADER_OUT));


print_r($output);
echo "\n";


