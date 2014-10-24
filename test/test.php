<?php

$file = __DIR__."/../data-exemple/timestamp-cert.pem";
$r = openssl_pkey_get_public(file_get_contents($file));

echo openssl_error_string();
echo $file."\n";
print_r(openssl_pkey_get_details($r));