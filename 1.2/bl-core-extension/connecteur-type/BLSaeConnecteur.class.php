<?php

require_once(PASTELL_PATH . "/pastell-core/Connecteur.class.php");

/**
 * Classe mère pour connecteurs de type SAE, destinés aux usages Berger-Levrault.
 */
abstract class BLSaeConnecteur extends Connecteur {

    // Attributs de service versant
    const ATTR_SAE_VERSANT_DESCRIPTION = 'versant_description';
    const ATTR_SAE_VERSANT_IDENTIFICATION = 'versant_identification';
    const ATTR_SAE_VERSANT_CONTACT_SERVICE = 'versant_contact_service';
    const ATTR_SAE_VERSANT_CONTACT_PERSONNE = 'versant_contact_personne';
    const ATTR_SAE_VERSANT_CONTACT_FONCTION = 'versant_contact_fonction';
    const ATTR_SAE_VERSANT_ADRESSE = 'versant_adresse';
    
    // Attributs de flux, spécifiques à ce type de connecteur; 
    // accessibles uniquement par les connecteurs de ce type.
    //
    const ATTR_SAE_JOURNAL_TRANSMISSIONS = 'journal_transmissions';
    const ATTR_SAE_BORDEREAU_SEDA = 'bordereau_seda';
    const ATTR_SAE_ARCHIVE_SEDA = 'archive_seda';
    const ATTR_SAE_AR_SEDA = 'ar_seda';
    const ATTR_SAE_AR_SEDA_COMMENTAIRE = 'ar_seda_commentaire';

    // Etats de versement
    const AR_ATTENTE = 'attente';
    const AR_OK = 'ok';
    const AR_REJET = 'rejet';
    
    public abstract function testConnexion();
    public abstract function genererSedaHelios();
    public abstract function verserSedaHelios();
    public abstract function arSedaHelios();
    
}
