<?php 


$soap = new SoapClient("https://iparapheur.demonstrations.adullact.org/ws-iparapheur-no-mtom?wsdl",
	
	array(
	     			'local_cert' => '/home/eric/adullact/pastell-workspace/connecteur_8.yml_iparapheur_user_key_pem_0',
	     			'passphrase' => 'adullact',
					'login' => 'wspastell@pastell',
					'password' => 'wspastell123',
					'trace' => 1,
					'exceptions' => 1,
	    		)

);