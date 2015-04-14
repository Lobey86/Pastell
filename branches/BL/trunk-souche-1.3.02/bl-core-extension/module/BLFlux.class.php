<?php

require_once(PASTELL_PATH . "/pastell-core/FluxSynchroneActionExecutor.class.php");

/**
 * Classe de traitements génériques sur flux, destinés aux usages Berger-Levrault.
 */
class BLFlux {
    // Attributs présents dans tous les flux, accessibles par les actions des
    // flux et par les connecteurs
    const ATTR_OBJET = FluxSynchroneActionExecutor::FLUX_ATTR_OBJET;
    const ATTR_APP_PILOTE = FluxSynchroneActionExecutor::FLUX_ATTR_PILOTE;
    const ATTR_ERREUR_DETAIL = FluxSynchroneActionExecutor::FLUX_ATTR_ERREUR_DETAIL;
    const ATTR_DERNIERE_TENTATIVE_ACTION = FluxSynchroneActionExecutor::FLUX_ATTR_DERNIERE_TENTATIVE_ACTION;
    const ATTR_EMETTEUR_EMAIL = 'emetteur_email';
    
    const ACTION_CLOTURE_NAME = 'cloture';
}
