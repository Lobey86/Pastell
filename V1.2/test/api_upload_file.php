<?php

$file_path = __DIR__."/annexe.pdf";

$post_data = array(
        "fichier_pes"=>"@$file_path",
		'objet' => 'toto',
		'id_d'=>'OhbQeEv',
		'id_e'=>'12',
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl,CURLOPT_URL,"http://192.168.1.5/adullact/pastell/web/api/modif-document.php");
curl_setopt($curl,CURLOPT_USERPWD,"admin:admin"); 
curl_setopt($curl, CURLOPT_POST,true);
curl_setopt($curl, CURLOPT_POSTFIELDS,$post_data);

$output = curl_exec($curl);

if ($err = curl_error($curl)){
	echo "Error : " . $err;
}	

echo $output."\n";