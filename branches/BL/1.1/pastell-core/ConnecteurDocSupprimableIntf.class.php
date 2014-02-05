<?php
/**
 * Interface décrivant un connecteur prenant en charge 
 * la suppression de documents.
 */ 
require_once(PASTELL_PATH . "/pastell-core/DonneesFormulaire.class.php");

interface ConnecteurDocSupprimableIntf {
    /**
     * Supprime un dossier.
     * @see Connecteur::getDocDonneesFormulaire()
     * @throws Exception si le dossier n'est pas dans un état permettant la suppression
     */
    public function docSupprimer();
    
}
