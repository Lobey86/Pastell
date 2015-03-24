<?php

require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/FileAttente.class.php");
require_once(__DIR__ . "/../../pastell-core/MailTo.class.php");

$now = time();
$msg="";
$notifier = false;

$list_file_attente = FileAttente::getAllFileAttente();
foreach($list_file_attente as $file_attente_courante) {
    $file_attente = new FileAttente($objectInstancier, WORKSPACE_PATH, $file_attente_courante['file'], $file_attente_courante['duree_attente']);
    $duree_attente = $file_attente->getDureeAttente();
    $duree_depasse = $file_attente->isFileAttenteWarning();
    $duree = $file_attente->getDureeLastMTime();
    if ($duree === false) {
        $notifier = true;
        $nature_notification ="CRITICAL";
        $msg = $msg . $file_attente->getFileAttenteName() . " : Le fichier de controle n'existe pas le serveur.\r\n";
    } else if ($duree > $duree_attente) {
        $date_fichier =$file_attente->getLastMtime();                
        $heure=floor($duree/3600);
        $minute=floor($duree%3600/60);
        $seconde=floor($duree%60);
        $duree_lisible = $heure . 'h ' . $minute . 'm ' . $seconde .'s';                        
        $msg = $msg . "   - " . $file_attente->getFileAttenteName() . " : La dernière écriture date de $date_fichier, ce qui correspond à $duree_lisible d'interruption.\r\n";
        $notifier = true;        
        if ($duree > ($duree_attente * 2)) {
            $nature_notification = "CRITICAL";
        } else {
            $nature_notification = "WARNING";
        }
    }     
}

if ($notifier) {
    $sujet = "[$nature_notification] " . FQDN . " :\r\nBusBL - Arrêt des scripts des actions automatiques";
    $contenu = "Le script des actions automatiques semblent ne plus s'exécuter sur le serveur " . FQDN . " : \r\n";
    $contenu = $contenu . $msg;
    $contenu = $contenu . "\r\n";
    $contenu = $contenu . "Merci de vérifier son bon fonctionnement.\r\n";
    $contenu = $contenu . "\r\n";
    $contenu = $contenu . "--\r\n";
    $contenu = $contenu . "Ce mail vous est envoyé automatiquement par le serveur " . FQDN . "."; 
    
    $mailto = new MailTo($objectInstancier);
    $mailto->mailRacineAdmins($sujet, $contenu, '');
    
}