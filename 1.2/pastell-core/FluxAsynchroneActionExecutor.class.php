<?php

require_once(PASTELL_PATH . "/pastell-core/FluxSynchroneActionExecutor.class.php");

/**
 * Enregistre la demande d'action de manière à pouvoir l'exécuter de manière 
 * asynchrone, c'est à dire plus tard.
 * La demande retourne toujours un OK fonctionnel. 
 */
abstract class FluxAsynchroneActionExecutor extends FluxSynchroneActionExecutor {
    /**
     * @deprecated since version 1.2.0
     */
    const ACTION_SYNCHRONE_DEFAUT=false;
    
    /*
     * L'attribut $synchroneActionName n'est plus utilisé. Il est conservé pour compatibilité ascendante.
     */
    public function __construct(ObjectInstancier $objectInstancier, $synchroneActionName=null) {
        parent::__construct($objectInstancier);            
    }
    
    protected function goFonctionnel() {
        return array(
            self::GO_KEY_ETAT => self::GO_ETAT_OK,
            self::GO_KEY_MESSAGE => self::GO_MESSAGE_ACTION);
    }

}