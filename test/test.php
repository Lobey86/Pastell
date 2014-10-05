<?php

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 5);
curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curlHandle, CURLOPT_SSLCERT, "/Users/eric/certificat/eric.crt");
curl_setopt($curlHandle, CURLOPT_SSLKEY, "/Users/eric/certificat/eric-p.key");
curl_setopt($curlHandle, CURLOPT_SSLKEYPASSWD, 'winfield');
curl_setopt($curlHandle, CURLOPT_CAINFO,"/Users/eric/certificat/rootCA.pem");
curl_setopt($curlHandle, CURLOPT_SSLVERSION, 3);

curl_setopt($curlHandle, CURLOPT_URL, 'https://localhost/');


$output = curl_exec($curlHandle);

print_r($output);
print_r(curl_error($curlHandle));
