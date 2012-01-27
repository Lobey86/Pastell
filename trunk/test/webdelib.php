<?php

ini_set('display_errors', 1);

/////////////////////////////////////
// Définition des variables
/////////////////////////////////////
define('login',    'col1');
define('password', 'col1');
//define('host',     'http://127.0.0.1/adullact/pastell');
define('host',     'http://pastell.sigmalis.com');
$id_e         = 3;
$circuit_id   = 2;

$delib['Deliberation']['nature_id']   = 1;
$delib['Deliberation']['objet_delib'] = 'Deliberation de test';
$delib['Seance']['date']              = '11/07/1981';
$delib['Deliberation']['num_delib']   = time();
$delib['Deliberation']['code']        =  '1.1.3';


$annexes[]                            = __DIR__."/annexe02.pdf";
$annexes[]                            = __DIR__."/annexe01.pdf";;

/////////////////////////////////////
// Actions envoyées à PASTELL
/////////////////////////////////////
$id_d   = createDocument($id_e);

echo "Création du document : $id_d ";

if (! $id_d){
	exit;
}

$result = modifyDocument($id_e, $id_d, $delib, $annexes);
insertInParapheur($id_e, $id_d);
insertInCircuit($id_e, $id_d, $circuit_id);

$result = action($id_e, $id_d, 'send-iparapheur');

print_r($result);

/////////////////////////////////////
// Définition des fonctions
/////////////////////////////////////

function _initCurl ($api, $data=array()) {
$curl = curl_init();
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
curl_setopt($curl, CURLOPT_USERPWD, login.":".password);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, host."/api/$api");
if (!empty($data)) {
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data );
}
return $curl;
}

function createDocument($id_e, $type='actes') {
$infos = array();
$curl = _initCurl("create-document.php?id_e=$id_e&type=$type");
$result = curl_exec($curl);
curl_close($curl);
$infos = json_decode($result);
$infos = (array) $infos;
return($infos['id_d']);
}

function modifyDocument($id_e, $id_d, $delib=array(), $annexes=array() ) {
$file =__DIR__."/delib.pdf";;

$acte = array('id_e'                    => $id_e,
'id_d'                    => $id_d,
'objet'                   => $delib['Deliberation']['objet_delib'],
'date_de_lacte'           => $delib['Seance']['date'],
'numero_de_lacte'         => $delib['Deliberation']['num_delib'],
'type'                    => $delib['Deliberation']['code'],
'arrete'                  => "@$file",
'acte_nature'             => $delib['Deliberation']['nature_id'],
);
$curl = _initCurl('modif-document.php', $acte);
$result = curl_exec($curl);
curl_close($curl);
foreach ($annexes as $annex)
sendAnnex($id_e, $id_d,  $annex);
}

function insertInParapheur($id_e, $id_d, $sous_type = null) {
$curl = _initCurl("modif-document.php?id_e=$id_e&id_d=$id_d&envoi_iparapheur=true");
$result = curl_exec($curl);
curl_close($curl);
}

function insertInCircuit($id_e, $id_d, $sous_type) {
$infos = array('id_e'                    => $id_e,
'id_d'                    => $id_d,
'iparapheur_sous_type'    => $sous_type);
$curl = _initCurl("modif-document.php", $infos);
$result = curl_exec($curl);
curl_close($curl);
}

function action($id_e, $id_d, $action) {
$acte = array('id_e'                    => $id_e,
'id_d'                    => $id_d,
'action'                  => $action
);
$curl = _initCurl('action.php', $acte);
$result = curl_exec($curl);
curl_close($curl);
return json_decode($result);
}

function sendAnnex($id_e, $id_d, $annex) {
$acte = array('id_e'                    => $id_e,
'id_d'                    => $id_d,
'autre_document_attache'  => "@$annex"
);
$curl = _initCurl('modif-document.php', $acte);
$result = curl_exec($curl);
curl_close($curl);
}

