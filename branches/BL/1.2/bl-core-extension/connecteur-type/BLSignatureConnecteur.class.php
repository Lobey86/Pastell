<?php

require_once(PASTELL_PATH . "/pastell-core/Connecteur.class.php");

/**
 * Classe mère pour connecteurs de type Signature, destinés aux usages Berger-Levrault.
 */
abstract class BLSignatureConnecteur extends Connecteur {

    // Attributs de flux, spécifiques à ce type de connecteur; 
    // accessibles uniquement par les connecteurs de ce type.
    const ATTR_SIGNATURE_CACHECIRCUITS = 'cache-circuits';
    const ATTR_SIGNATURE_DOSSIERID = 'parapheur_dossierid';
    const ATTR_SIGNATURE_DELETED = 'parapheur_deleted';

}
