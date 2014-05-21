<?php

require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$offset = $recuperateur->getInt('offset',0);
$limit = $recuperateur->getInt('limit',100);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');
$id_d = $recuperateur->get('id_d');
$id_user = $recuperateur->get('id_user');
$recherche = $recuperateur->get('recherche');
$date_debut = $recuperateur->get('date_debut');
$date_fin = $recuperateur->get('date_fin');
$format = $recuperateur->get('format');
$nbreLigneMaxParPaquet = $recuperateur->get('nbre_ligne_max_paquet', 10000);

if   (! $roleUtilisateur->hasDroit($id_u,"journal:lecture",$id_e)){
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u,type=$type");
}

// Pour éviter des problèmes mémoires, au format CSV : 
//  - le chargement des lignes se fait par paquet.
//  - les lignes sont écrites dans le fichier CSV au fur et à mesure des chargements des paquets.
//  - comme les traitements sont plus long, réinitialisation du temps max_execution_time dans chaque boucle.
// NB : Le problème "mémoire", existe toujours pour le format JSON.

if ($format == 'csv') {
    if ($limit!=-1 && $limit < $nbreLigneMaxParPaquet) {    
        $nbreLigne=$limit;
    } else {
        $nbreLigne=$nbreLigneMaxParPaquet;
    }
    $ligneDepart = $offset;
    $all = array();
    $filecsv = tempnam('/tmp/', 'exportjournal');
    $handle = fopen($filecsv, 'w');
    $continuer = true;
    $max_execution_time= ini_get('max_execution_time');
    while ($continuer) {
        ini_set('max_execution_time', $max_execution_time);
        $tab = $journal->getAll($id_e, $type, $id_d, $id_user, $ligneDepart, $nbreLigne, $recherche, $date_debut, $date_fin, true);
        $nbreLigne = sizeof($tab);
        $ligneDepart = $ligneDepart + $nbreLigne;
        foreach ($tab as $ligne) {
            $ligne['message'] = preg_replace("/(\r\n|\n|\r)/", " ", $ligne['message']);
            $ligne['message_horodate'] = preg_replace("/(\r\n|\n|\r)/", " ", $ligne['message_horodate']);
            unset($ligne['preuve']);
            fputcsv($handle, $ligne);
        }
        $tab = null;        
        $continuer = (($limit==-1) || ($limit > $nbreLigneMaxParPaquet)) && ($nbreLigne == $nbreLigneMaxParPaquet);
        if( $continuer) {
            // Calcul du nombre de ligne de la prochaine itération.
            if (($limit != -1) && (($ligneDepart + $nbreLigne) > $limit)) {
                $nbreLigne = $limit - $ligneDepart;
            }
        }
    }   
    fclose($handle);
    header("Content-type: text/csv; charset=iso-8859-1");
    header("Content-disposition: attachment; filename=pastell-export-journal-$id_e-$type-$id_d.csv");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    header("Pragma: public");
    readfile($filecsv);

    unlink($filecsv);
} else {
    $all = $journal->getAll($id_e, $type, $id_d, $id_user, $offset, $limit, $recherche, $date_debut, $date_fin);
    if ($format == 'csv') {
        $CSVoutput = new CSVoutput();
        $CSVoutput->sendAttachment("pastell-export-journal-$id_e-$type-$id_d.csv", $all);
    } else {
        $JSONoutput->display($all);
    }
}
