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
    
    public abstract function getListTypes();
    public abstract function getListCircuits($type);
    public abstract function getCircuitDetail($type, $circuit);
    public abstract function sendDocument();
    public abstract function sendHeliosDocument();
    public abstract function sendActeDocument();
    /** @link ConnecteurSignatureDocHistorisableIntf */
    public abstract function docHistorique();
    public abstract function searchHistoriqueLogValidation($histoAll);
    public abstract function searchHistoriqueLogRejet($histoAll);
    public abstract function getSignature();
    
    /**
     * Normalise une date au format Pastell
     * @param type $datetime
     * @return type
     */
    protected function datetimeIsoToPastell($datetime) {
        if (empty($datetime)) {
            return $datetime;
        }
        $datetimePhp = is_string($datetime) ? strtotime($datetime) : $datetime;
        $datetimePastell = date('Y-m-d\TH:i:s\.000P', $datetimePhp);
        return $datetimePastell;
    }
}
