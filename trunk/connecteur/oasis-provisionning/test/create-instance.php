<?php


$url = "https://kernel.ozwillo-preprod.eu/apps/pending-instance/bc45bc20-cfdf-4a2e-a52b-f00ed3165bbe";
//$url = "http://www.yahoo.fr/";
$client_id = "bc45bc20-cfdf-4a2e-a52b-f00ed3165bbe";
$client_secret = "3Z6hz8ah0i8p3YBhJhyPg3CZK63WwI5AbqbByIcpJ+w";

$data = array(
		"services"=>array(array('local_id'=>'pastell',
				"service_uri" => 'http://dev.sigmalis.com/pastell/oasis/connexion.php?id_e=1',
				"visible" => true,
				"name" => "Pastell",
				"description" => false,
				"tos_uri"=>"http://dev.sigmalis.com/pastell",
				"policy_uri"=>"http://dev.sigmalis.com/pastell",
				"icon"=>"http://dev.sigmalis.com/pastell",
				"contacts" => array("http://dev.sigmalis.com/pastell"),
				"payment_option"=>"FREE",
				"target_audience" => array("PUBLIC_BODIES"),
				"redirect_uris" => array("http://dev.sigmalis.com/pastell"),
		)),
			"instance_id"=>"bc45bc20-cfdf-4a2e-a52b-f00ed3165bbe",
		
		
		); 

echo json_encode($data);


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


print_r($output);
echo "\n";
