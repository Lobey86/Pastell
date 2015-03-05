<?php

require_once(PASTELL_PATH . '/bl-core-extension/module/BLFlux.class.php');

/**
 * Classe de traitements génériques sur flux documents internes, destinés aux usages Berger-Levrault.
 */
class BLDocInterneFlux extends BLFlux {
    // Attributs spécifiques à ce flux, accessibles par les actions de ce flux 
    // et par les connecteurs
    const ATTR_TYPE = 'iparapheur_type';
    const ATTR_SOUS_TYPE = 'iparapheur_sous_type';
    const ATTR_FICHIER_PRINCIPAL = 'document';
    const ATTR_VISUEL_PDF = 'visuel_pdf';
    const ATTR_DATE_LIMITE = 'parapheur_date_limite';
    const ATTR_ANNOTATION_ENVOI = 'parapheur_annotation_envoi';
    const ATTR_ANNOTATION_RETOUR = 'parapheur_annotation_retour';
}
