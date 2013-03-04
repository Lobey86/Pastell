<?php 

$server_name = "extranet.adullact.org";
$login = "megalis";
$pubkey_file = "/tmp/id_rsa_megalis.pub";
$priv_key_file = "/tmp/id_rsa_megalis-unprotected";
$directory = "/home/megalis";

$ssh_connexion = ssh2_connect($server_name);
if( ! $ssh_connexion){
	exit;
}
echo "Connexion réseau : ok\n";

$auth = ssh2_auth_pubkey_file($ssh_connexion,$login,$pubkey_file,$priv_key_file);
if (!$auth){
	exit;
}
echo "Authentification : ok\n";

$sftp = ssh2_sftp($ssh_connexion);
if (!$sftp){
	exit;
}
echo "SFTP : ok\n";

$result = scandir("ssh2.sftp://{$sftp}{$directory}");

print_r($result);

