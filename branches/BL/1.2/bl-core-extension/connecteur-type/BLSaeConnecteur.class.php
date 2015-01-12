<?php

require_once(PASTELL_PATH . "/pastell-core/Connecteur.class.php");

/**
 * Classe mère pour connecteurs de type SAE, destinés aux usages Berger-Levrault.
 */
abstract class BLSaeConnecteur extends Connecteur {

    // Attributs de flux, spécifiques à ce type de connecteur; 
    // accessibles uniquement par les connecteurs de ce type.
    //
    const ATTR_SAE_JOURNAL_TRANSMISSIONS = 'journal_transmissions';
    const ATTR_SAE_BORDEREAU_SEDA = 'bordereau_seda';
    const ATTR_SAE_ARCHIVE_SEDA = 'archive_seda';
    const ATTR_SAE_AR_SEDA = 'ar_seda';
    const ATTR_SAE_AR_SEDA_COMMENTAIRE = 'ar_seda_commentaire';

    public abstract function testConnexion();
    public abstract function genererSedaHelios();
    public abstract function deposerSedaHelios();
    public abstract function verifierEtatDepotSedaHelios();
    
}
