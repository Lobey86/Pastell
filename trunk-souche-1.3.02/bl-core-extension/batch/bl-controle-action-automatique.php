<?php

require_once( __DIR__ . "/../../web/init.php");
require_once(__DIR__ . "/../../pastell-core/MailTo.class.php");

$delai_depasse = $objectInstancier->LastUpstart->hasWarning();

if ($delai_depasse) {
    $now=time();
    // Détermination de la durée    
    $date_fichier = $objectInstancier->LastUpstart->getLastMtime();
    if ($date_fichier) {
        $fichier_time = strtotime($date_fichier);
        $delai = ($now - $fichier_time);
        // Délai lisible                
        $heure=floor($delai/3600);
        $minute=floor($delai%3600/60);
        $seconde=floor($delai%60);
        $delai_lisible = $heure . 'h ' . $minute . 'm ' . $seconde .'s';
    }
    // Rédaction du mail
    // Le mail est envoyé aux utilisateurs ayant le role admin sur l'entité racine.    
    $sujet = "[ALERTE] " . FQDN . " :\r\nBusBL - Arrêt du script des actions automatiques";
    $contenu = "Le script des actions automatiques semble ne plus s'exécuter sur le serveur " . FQDN . " : \r\n";
    if ($date_fichier) {
        $contenu = $contenu . "La dernière écriture date de $date_fichier ce qui correspond à $delai_lisible d'interruption.\r\n";
    } else {
        $contenu = $contenu . "Le fichier de controle n'existe pas sur le serveur.";
    }
    $contenu = $contenu . "\r\n";
    $contenu = $contenu . "Merci de vérifier son bon fonctionnement.\r\n";
    $contenu = $contenu . "\r\n";
    $contenu = $contenu . "--\r\n";
    $contenu = $contenu . "Ce mail vous est envoyé automatiquement par le serveur " . FQDN . "."; 
    
    $mailto = new MailTo($objectInstancier);    
    $mailto->mailRacineAdmins($sujet, $contenu, '');
    
}
