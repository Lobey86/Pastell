<?php

require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/BLBatch.class.php");

$blscript = new BLBatch();

$totaux = array(
    'global_count' => 0,
    'entite_count' => 0,
    'document_count' => 0
);

$fmesure = function() use (&$blscript, &$objectInstancier, &$sqlQuery, &$totaux) {
    // Effectuer les actions des connecteurs globaux.

    $all_connecteur = $objectInstancier->ConnecteurEntiteSQL->getAll(0);

    foreach ($all_connecteur as $connecteur) {
        $documentType = $objectInstancier->DocumentTypeFactory->getGlobalDocumentType($connecteur['id_connecteur']);
        $all_action = $documentType->getAction()->getAutoAction();
        foreach ($all_action as $action) {
            $fmesure = function() use (&$blscript, &$objectInstancier, &$totaux, &$connecteur, &$action) {
                $blscript->checkBatchStop();
                $totaux['global_count'] ++;
                $id_ce = $connecteur['id_ce'];
                $id_e = $connecteur['id_e'];
                $name_ce = $connecteur['id_connecteur'];
                $type_ce = $connecteur['type'];
                $blscript->trace($blscript->heure() . " id_ce=$id_ce, id_e=$id_e, type_ce=$type_ce, name_ce=$name_ce, action=$action,");
                $result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce, 0, $action, true, array());
                if (!$result) {
                    return $objectInstancier->ActionExecutorFactory->getLastMessage();
                }
                return 'ok';
            };
            list($result, $duree, $mem) = $blscript->mesurer($fmesure);
            $blscript->traceln(" durée=$duree, mem=$mem");
            $blscript->traceln($result);
            $objectInstancier->LastUpstart->updateMtime();
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
                        $blscript->checkBatchStop();
                        $totaux['entite_count'] ++;
                        $id_ce = $connecteur_entite['id_ce'];
                        $id_e = $connecteur_entite['id_e'];
                        $name_ce = $connecteur_entite['id_connecteur'];
                        $type_ce = $connecteur_entite['type'];
                        $blscript->trace($blscript->heure() . " id_ce=$id_ce, id_e=$id_e, type_ce=$type_ce, name_ce=$name_ce, action=$action_auto,");
                        $result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce, 0, $action_auto, true, array());
                        if (!$result) {
                            return $objectInstancier->ActionExecutorFactory->getLastMessage();
                        }
                        return "ok";
                    };
                    list($result, $duree, $mem) = $blscript->mesurer($fmesure);
                    $blscript->traceln(" durée=$duree, mem=$mem");
                    $blscript->traceln($result);
                    $objectInstancier->LastUpstart->updateMtime();
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
                    $blscript->checkBatchStop();
                    $totaux['document_count'] ++;
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
                    $objectInstancier->ActionExecutorFactory->executeOnDocument($id_e, 0, $id_d, $etat_cible, array(), true, array());
                    $message = $objectInstancier->ActionExecutorFactory->getLastMessage();
                    $objectInstancier->ActionAutoLogSQL->add($id_e, $id_d, $etat_actuel, $etat_cible, $message);
                    return $message;
                };
                list($result, $duree, $mem) = $blscript->mesurer($fmesure);
                $blscript->traceln(" durée=$duree, mem=$mem");
                $blscript->traceln($result);
                $objectInstancier->LastUpstart->updateMtime();
            }
        }
    }
};

list($result, $duree, $mem) = $blscript->mesurer($fmesure);
$blscript->traceln($blscript->heure() . " TOTAL : global_count={$totaux['global_count']}, entite_count={$totaux['entite_count']}, document_count={$totaux['document_count']}, durée=$duree, mem=$mem");

$objectInstancier->LastUpstart->updateMtime();
