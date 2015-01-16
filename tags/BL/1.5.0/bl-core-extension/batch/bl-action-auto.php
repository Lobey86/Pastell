<?php
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/BLBatch.class.php");

$blscript = new BLBatch();
$zenMail = new ZenMail();
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification, $zenMail, $journal);

// Effectuer les actions des connecteurs (qui mettent à jour les caches) 
// avant les actions des flux (qui utilisent ces caches).

$all_connecteur = $objectInstancier->ConnecteurEntiteSQL->getAll(0);

foreach ($all_connecteur as $connecteur) {
    $documentType = $objectInstancier->DocumentTypeFactory->getGlobalDocumentType($connecteur['id_connecteur']);
    $all_action = $documentType->getAction()->getAutoAction();
    foreach ($all_action as $action) {
        $blscript->checkBatchStop();
        $blscript->traceln($blscript->heure() . " Traitement sur connecteur ({$connecteur['id_ce']}, {$connecteur['libelle']}, $action) : ");
        $result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($connecteur['id_ce'], 0, $action, true, array());
        if (!$result) {
            $blscript->traceln($objectInstancier->ActionExecutorFactory->getLastMessage());
        } else {
            $blscript->traceln("ok");
        }
    }
}

$documentEntite = new DocumentEntite($sqlQuery);
$tabFluxChange = array();

foreach ($objectInstancier->fluxDefinitionFiles->getAll() as $type => $config) {
    $tabAction = $objectInstancier->DocumentTypeFactory->getFluxDocumentType($type)->getAction()->getAutoAction();
    foreach ($tabAction as $etat_actuel => $etat_cible) {
        foreach ($documentEntite->getFromAction($type, $etat_actuel) as $infoDocument) {
            $blscript->checkBatchStop();
            $id_d = $infoDocument['id_d'];
            $id_e = $infoDocument['id_e'];
            $fluxChangeCour = "$etat_actuel->$etat_cible";
            $blscript->traceln($blscript->heure() . " Traitement sur document ($id_e,$id_d,$type,$fluxChangeCour)");
            // On ne manipule pas 2 fois le même flux au cours du même cycle de traitement des actions automatiques.
            // Ceci évite les effets de bord dus à des actions concurrentes, dont les modifications ne seraient pas 
            // prises en compte dans le cache des documents (DonneesFormulaireFactory->getFromCache).            
            $fluxChangePrec = @$tabFluxChange[$id_d];
            if (isset($fluxChangePrec)) {
                $blscript->traceln("Reporté : déjà modifié au cours de ce cycle ($fluxChangePrec)");
            } else {
                $tabFluxChange[$id_d] = $fluxChangeCour;
                // On ne traite le document que s'il n'a pas changé d'état.
                $lastAction = $objectInstancier->DocumentActionEntite->getTrueAction($id_e, $id_d);
                if (!$lastAction) {
                    $blscript->traceln("Annulé : le dossier a été supprimé par une action concurrente");
                } elseif ($lastAction == $etat_actuel) {
                    $result = $objectInstancier->ActionExecutorFactory->executeOnDocument($id_e, 0, $id_d, $etat_cible, array(), true, array());
                    $message = $objectInstancier->ActionExecutorFactory->getLastMessage();
                    $objectInstancier->ActionAutoLogSQL->add($id_e, $id_d, $etat_actuel, $etat_cible, $message);
                    $blscript->traceln($message);
                } else {
                    $blscript->traceln("Annulé : état changé en $lastAction par une action concurrente");
                }
            }
        }
    }
}

$objectInstancier->LastUpstart->updateMtime();
