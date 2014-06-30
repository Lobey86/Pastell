<?php

require_once(PASTELL_PATH . "/connecteur-type/TdtConnecteur.class.php");

/**
 * Classe mère pour connecteurs de type TdT, destinés aux usages Berger-Levrault.
 */
abstract class BLTdtConnecteur extends TdtConnecteur {

    const KEY_INFO_FILESIZE = 'filesize';
    const ATTR_TRANSACTION_ANNULATION_ID = "tdt_transaction_annulation_id";

    public function isUsageActe() {
        $connecteur_data = $this->getConnecteurConfig()->getRawData();
        if (!isset($connecteur_data['usage_acte']) || $connecteur_data['usage_acte']) {
            return true;
        }
        return false;
    }

}
