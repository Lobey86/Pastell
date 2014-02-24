<?php

require_once(__DIR__ . "/BLActionExecutor.class.php");

/**
 * Encadre les traitements fonctionnels d'une action de connecteur en prenant en charge :
 * - la détection des erreurs d'accès aux services et le pilotage des 
 *   suspension/reprises du connecteur concerné
 * - la détection des services désactivés
 * - le log dans le journal, pour les actions API
 * Offre des méthodes déclenchables par les actions dérivées : 
 * - émission de notification
 * Restent à la charge du fonctionnel :
 * - le traitement, accédant au(x) service(s)
 * - le calcul du résultat (message, informations, ...)
 * - redéfinition du formattage d'affichage par défaut, pour les actions d'obtention d'information
 */
abstract class BLConnecteurActionExecutor extends BLActionExecutor {

    // Valeurs conventionnées pour GO_KEY_*

    const GO_ETAT_SUCCES = 'etat-ok';
    const GO_ETAT_ECHEC = 'etat-nok';

    /**
     * Exécute le traitement fonctionnel de l'action.
     * Les conventions de retours sont décrites par chaque implémentation.
     * <p>
     * En cas d'exception, l'action est considérée en échec. 
     * Le texte de l'exception sert de message.
     * <br>
     * En cas d'absence d'exception, le retour détermine le résultat de l'action.
     * <p>
     * @return mixed booléen, string ou array
     *         <ul>
     *         <li>booléen
     *              true pour un succès, false pour un échec.<br>
     *              Le texte du message est lu dans @link self::getLastMessage
     *              </li>
     *         <li>string
     *              L'action est un succès. Le retour est le texte du message.
     *              </li>
     *         <li>array
     *              <ul>
     *              <li>GO_KEY_ETAT => résultat de l'action<br>
     *                  GO_ETAT_SUCCES ou true pour un succès<br>
     *                  GO_ETAT_ECHEC ou false pour un échec<br>
     *                  </li>
     *              <li>GO_KEY_MESSAGE => message du résultat.<br>
     *                  </li>
     *              </ul>
     *              </li>
     *         </ul>
     */
    abstract protected function goFonctionnel();

    public function go() {
        try {
            $gof = $this->goFonctionnel();
            if (is_array($gof)) {
                $gofEtat = $gof[self::GO_KEY_ETAT];
                if ($gofEtat == self::GO_ETAT_SUCCES || $gofEtat === true) {
                    $gofEtat = true;
                } elseif ($gofEtat == self::GO_ETAT_ECHEC || $gofEtat === false) {
                    $gofEtat = false;
                } else {
                    throw new Exception("Format de retour 'goFonctionnel' incorrect");
                }
                $gofMessage = $gof[self::GO_KEY_MESSAGE];
            } elseif (is_string($gof)) {
                $gofEtat = true;
                $gofMessage = $gof;
            } else {
                $gofEtat = $gof;
                $gofMessage = $this->getLastMessage();
            }
            $this->setLastMessage($gofMessage);
            return $gofEtat;
        } catch (ConnecteurAccesException $gofEx) {
            // Gestion des suspensions
            try {
                $this->objectInstancier->ConnecteurSuspensionControler->onAccesEchec($gofEx->getConnecteur(), $gofEx);
            } catch (Exception $onAccesEchecEx) {
                // Erreur de gestion des suspensions => erreur tracée
                $this->throwException($onAccesEchecEx);
            }
            // Erreur tracée
            $this->throwException($gofEx);
        } catch (Exception $gofEx) {
            // Erreur fonctionnelle => erreur tracée, état d'erreur
            $this->throwException($gofEx);
        }
    }

    private function throwException(Exception $ex) {
        $messageDetail = parent::exceptionToJson($ex);
        $connecteurConfig = $this->getConnecteurProperties();
        $connecteurConfig->addFileFromData(self::ATTR_ERREUR_DETAIL, 'erreur_detail', $messageDetail);
        throw $ex;
    }

}
