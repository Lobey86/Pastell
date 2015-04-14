<?php

require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/BLBatch.class.php");
require_once( __DIR__ . "/ActionAutoControler.class.php");

$blscript = new BLBatch();

$totaux = array(
    'global_count' => 0,
    'entite_count' => 0,
    'document_count' => 0
);

$file_attente_name = $blscript->getArg('file', 'DEFAULT');
$duree_attente = $blscript->getArg('duree_attente', '1800');

$action_auto_controler = new ActionAutoControler($objectInstancier, $file_attente_name, $duree_attente);

$action_auto_controler->demarrerExecutionFileAttente();

$fmesure = function() use (&$blscript, &$objectInstancier, &$sqlQuery, &$totaux, &$action_auto_controler) {
    // Effectuer les actions des connecteurs globaux.

    $all_connecteur = $objectInstancier->ConnecteurEntiteSQL->getAll(0);

    foreach ($all_connecteur as $connecteur) {
        $documentType = $objectInstancier->DocumentTypeFactory->getGlobalDocumentType($connecteur['id_connecteur']);
        $all_action = $documentType->getAction()->getAutoAction();
        foreach ($all_action as $action) {
            $fmesure = function() use (&$blscript, &$objectInstancier, &$totaux, &$connecteur, &$action) {                                
                $id_ce = $connecteur['id_ce'];
                $id_e = $connecteur['id_e'];
                $name_ce = $connecteur['id_connecteur'];
                $type_ce = $connecteur['type'];
                $blscript->trace($blscript->heure() . " id_ce=$id_ce, id_e=$id_e, type_ce=$type_ce, name_ce=$name_ce, action=$action,");
                $totaux['global_count'] ++; 
                $result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce, 0, $action, true, array());
                if (!$result) {
                    return $objectInstancier->ActionExecutorFactory->getLastMessage();
                }
                return 'ok';
            };
            $blscript->checkBatchStop();
            $action_auto_controler->checkFileAttenteStop();                       
            $executable = $action_auto_controler->isActionConnecteurExecutable($connecteur['id_ce'], $action);
            if ($executable) {            
                list($result, $duree, $mem) = $blscript->mesurer($fmesure);            
                $blscript->traceln(" durée=$duree, mem=$mem");
                $blscript->traceln($result);
            }
            $action_auto_controler->majMtime();
        }
    }

    // Effectuer les actions automatiques des connecteurs entités (qui mettent à jour les caches) 
    // avant les actions des flux (qui utilisent ces caches).
    // 
    // Chargement des types de connecteur (id_connecteur)
    $list_id_connecteur = $objectInstancier->ConnecteurEntiteSQL->getAllId();
    foreach ($list_id_connecteur as $id_connecteur) {
        // On ne traite que les types de connecteur qui ont des actions automatiques dans leur définition (properties.yml).
        $documentType = $objectInstancier->DocumentTypeFactory->getEntiteDocumentType($id_connecteur['id_connecteur']);
        $all_action_auto = $documentType->getAction()->getAutoAction();
        if ($all_action_auto) {
            // Chargement de tous les connecteurs entités de ce type pour exécuter les actions automatiques
            $list_connecteur_entite = $objectInstancier->ConnecteurEntiteSQL->getAllById($id_connecteur['id_connecteur']);
            // Execution des actions automatiques des connecteurs entités du type en cours.
            foreach ($all_action_auto as $action_auto) {
                foreach ($list_connecteur_entite as $connecteur_entite) {
                    $fmesure = function() use (&$blscript, &$objectInstancier, &$totaux, &$connecteur_entite, &$action_auto) {                        
                        $id_ce = $connecteur_entite['id_ce'];
                        $id_e = $connecteur_entite['id_e'];
                        $name_ce = $connecteur_entite['id_connecteur'];
                        $type_ce = $connecteur_entite['type'];                        
                        $blscript->trace($blscript->heure() . " id_ce=$id_ce, id_e=$id_e, type_ce=$type_ce, name_ce=$name_ce, action=$action_auto,");
                        $totaux['entite_count'] ++;
                        $result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce, 0, $action_auto, true, array());
                        if (!$result) {
                            return $objectInstancier->ActionExecutorFactory->getLastMessage();
                        }
                        return "ok";                       
                    };
                    $blscript->checkBatchStop();     
                    $action_auto_controler->checkFileAttenteStop();
                    $executable = $action_auto_controler->isActionConnecteurExecutable($connecteur_entite['id_ce'], $action_auto);
                    if ($executable) {
                        list($result, $duree, $mem) = $blscript->mesurer($fmesure);
                        $blscript->traceln(" durée=$duree, mem=$mem");
                        $blscript->traceln($result);
                    }
                    $action_auto_controler->majMtime();
                }
            }
        }
    }

    $documentEntite = new DocumentEntite($sqlQuery);
    $tabFluxChange = array();

    foreach ($objectInstancier->fluxDefinitionFiles->getAll() as $type => $config) {
        $tabAction = $objectInstancier->DocumentTypeFactory->getFluxDocumentType($type)->getAction()->getAutoAction();
        foreach ($tabAction as $etat_actuel => $etat_cible) {
            foreach ($documentEntite->getFromAction($type, $etat_actuel) as $infoDocument) {
                $fmesure = function() use (&$blscript, &$objectInstancier, &$totaux, &$tabFluxChange, &$infoDocument, &$type, &$etat_actuel, &$etat_cible) {                                        
                    $id_d = $infoDocument['id_d'];
                    $id_e = $infoDocument['id_e'];
                    $blscript->trace($blscript->heure() . " id_d=$id_d, id_e=$id_e, type_d=$type, action=$etat_cible,");
                    // On ne manipule pas 2 fois le même flux au cours du même cycle de traitement des actions automatiques.
                    // Ceci évite les effets de bord dus à des actions concurrentes, dont les modifications ne seraient pas 
                    // prises en compte dans le cache des documents (DonneesFormulaireFactory->getFromCache).            
                    $etat_prec = @$tabFluxChange[$id_d];
                    if (isset($etat_prec)) {
                        return "Reporté : déjà modifié au cours de ce cycle ($etat_prec)";
                    }
                    $tabFluxChange[$id_d] = $etat_cible;
                    // On ne traite le document que s'il n'a pas changé d'état.
                    $lastAction = $objectInstancier->DocumentActionEntite->getTrueAction($id_e, $id_d);
                    if (!$lastAction) {
                        return "Annulé : le dossier a été supprimé par une action concurrente";
                    }
                    if ($lastAction != $etat_actuel) {
                        return "Annulé : état changé en $lastAction par une action concurrente";
                    }                    
                             
                    $totaux['document_count'] ++;
                    $objectInstancier->ActionExecutorFactory->executeOnDocument($id_e, 0, $id_d, $etat_cible, array(), true, array());
                    $message = $objectInstancier->ActionExecutorFactory->getLastMessage();
                    $objectInstancier->ActionAutoLogSQL->add($id_e, $id_d, $etat_actuel, $etat_cible, $message);
                    return $message;

                };

                $blscript->checkBatchStop();
                $action_auto_controler->checkFileAttenteStop();
                $executable = $action_auto_controler->isActionDocumentExecutable($infoDocument['id_e'], $infoDocument['id_d'], $type, $etat_cible);                    
                if ($executable) {                
                    list($result, $duree, $mem) = $blscript->mesurer($fmesure);
                    $blscript->traceln(" durée=$duree, mem=$mem");
                    $blscript->traceln($result);
                }
                $action_auto_controler->majMtime();
            }
        }
    }
};

// Calcul de la prochaine execution du script
$date_prochaine_execution = new DateTime();
$date_prochaine_execution->add(new DateInterval('PT' . $duree_attente . 'S'));

list($result, $duree, $mem) = $blscript->mesurer($fmesure);
$blscript->traceln($blscript->heure() . " TOTAL : global_count={$totaux['global_count']}, entite_count={$totaux['entite_count']}, document_count={$totaux['document_count']}, durée=$duree, mem=$mem");

if ($duree>=$duree_attente) {
    $prochaine_execution = 'maintenant';
} else {
    $prochaine_execution = $date_prochaine_execution->format('d/m/Y H:i:s');
}

$blscript->traceln($blscript->heure() . " Duree execution : " . $duree . " - Prochaine : " . $prochaine_execution);

// Suppression du fichier flag
$action_auto_controler->arreterExecutionFileAttente();

$action_auto_controler->majMtime();
