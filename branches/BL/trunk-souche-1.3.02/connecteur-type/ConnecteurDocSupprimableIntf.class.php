<?php

/**
 * Interface décrivant un connecteur prenant en charge 
 * la suppression de documents.
 */ 
require_once(PASTELL_PATH . "/pastell-core/DonneesFormulaire.class.php");

interface ConnecteurDocSupprimableIntf {

    const SUPPR_KEY_ETAT = 'etat';
    const SUPPR_KEY_MESSAGE = 'message';
    const SUPPR_ETAT_OK = 'ok';
    const SUPPR_ETAT_REPORT = 'report';

    /**
     * Supprime un dossier.
     * @see Connecteur::getDocDonneesFormulaire()
     * @return mixed
     *         <ul>
     *         <li>array
     *              <ul>
     *              <li>SUPPR_KEY_ETAT => résultat d'une suppression sans échec<br>
     *                  SUPPR_ETAT_OK : la suppression a réussi<br>
     *                  SUPPR_ETAT_REPORT : le dossier sur le service n'est pas 
     *                      dans un état permettant la suppression immédiate; mais
     *                      son état pourra changer et la suppression être tentée 
     *                      à nouveau.<BR/> 
     *                      L'état du dossier bus reste donc inchangé, aucune erreur
     *                      n'est signalée.
     *                  </li>
     *              <li>SUPPR_KEY_MESSAGE => message du résultat d'une suppression sans échec<br>
     *                  </li>
     *              </ul>
     *              </li>
     *         <li>autre
     *              équivalent SUPPR_ETAT_OK, sans message
     *              </li>
     *         </ul>
     * 
     * @throws Exception Echec de la suppression sur le service.
     */
    public function docSupprimer();
}
