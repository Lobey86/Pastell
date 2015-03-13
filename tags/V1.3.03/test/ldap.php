<?php

$ldap_host = "localhost";
$ldap_port = "10389";
$ldap_user = "uid=admin,ou=system";
$ldap_password="secret";
$dn = "dc=example,dc=com";
$filter = "(objectClass=*)";

$ldap = ldap_connect($ldap_host,$ldap_port);
if (!$ldap){
	throw new Exception("Impossible de se connecter sur le serveur LDAP : " . ldap_error($ldap));
}
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
if (! ldap_bind($ldap,$ldap_user,$ldap_password)){
	throw new Exception("Impossible de s'authentifier sur le serveur LDAP : ".ldap_error($ldap));
}

$result = ldap_search($ldap,$dn,$filter);

echo ldap_count_entries($ldap,$result) ."\n";
	