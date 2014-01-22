<?php

require_once(PASTELL_PATH . "/pastell-core/FluxSynchroneActionExecutor.class.php");

/**
 * Enregistre la demande d'action de manière à pouvoir l'exécuter de manière 
 * asynchrone, c'est à dire plus tard.
 * 
 * Une tentative immédiate est tout de même tentée. 
 * 
 * En cas de succès, ceci permet : 
 * - d'économiser le délai avant déclenchement des opérations automatiques
 * - de lisser les accès aux services dans le temps, et éviter les pics 
 *   induits par les accès rapprochés durant les opérations automatiques.
 * 
 * La demande retourne toujours un OK fonctionnel. Ainsi, si la tentative d'action 
 * immédiate a échoué, l'erreur reste tracée, mais elle n'apparait pas dans le 
 * retour de la demande.
 */
abstract class FluxAsynchroneActionExecutor extends FluxSynchroneActionExecutor {
    const ACTION_SYNCHRONE_DEFAUT = false;
    
    private $synchroneActionName;

    public function __construct(ObjectInstancier $objectInstancier, $synchroneActionName) {
        parent::__construct($objectInstancier);        
        $this->synchroneActionName = $synchroneActionName;
    }

    public function go() {
        // Dans tous les cas, la demande est enregistrée.
        $goRet = parent::go();
        // Tentative immédiate effectuée
        try {
            if ($this->synchroneActionName === self::ACTION_SYNCHRONE_DEFAUT) {
                $this->synchroneActionName = substr($this->action, 0, strlen($this->action) - strlen('-demande'));
            }
            $this->objectInstancier->ActionExecutorFactory->executeOnDocumentThrow(
                    $this->id_d, 
                    $this->id_e, 
                    $this->id_u, 
                    $this->synchroneActionName, 
                    $this->id_destinataire,
                    $this->from_api,
                    $this->action_params);
        } catch (Exception $gofEx) {
            // Le résultat de l'action synchrone a été enregistré. Selon l'état 
            // appliqué, elle pourrait être retentée par les opérations automatiques.
            // On ne remonte pas l'erreur car le retour doit concerner la demande, 
            // qui a bien été enregistrée.
        }
        return $goRet;
    }

    protected function goFonctionnel() {
        return array(
            self::GO_KEY_ETAT => self::GO_ETAT_OK,
            self::GO_KEY_MESSAGE => self::GO_MESSAGE_ACTION);
    }

}