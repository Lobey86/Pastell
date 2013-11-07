<?php

class ConnecteurSuspensionControler {

    const ATTR_TENTATIVES_CONTEXT = 'tentatives-context';
    const ATTR_SUSPENSION = 'acces_suspendu';

    private $objectInstancier;

    public function __construct(ObjectInstancier $objectInstancier) {
        $this->objectInstancier = $objectInstancier;
    }

    public function isSuspension(ConnecteurSuspensionIntf $connecteur) {
        $connecteurConfig = $connecteur->getConnecteurConfig();
        $suspension = $connecteurConfig->get(self::ATTR_SUSPENSION, false);
        return $suspension;
    }

    public function setSuspension(ConnecteurSuspensionIntf $connecteur, $suspension) {
        $connecteurConfig = $connecteur->getConnecteurConfig();
        $oldSuspension = $connecteurConfig->get(self::ATTR_SUSPENSION, false);
        if ($oldSuspension != $suspension) {
            $connecteurConfig->setData(self::ATTR_SUSPENSION, $suspension);
            if (!$suspension) {
                // Réinitialiser le contexte des tentatives
                $fContext = $connecteurConfig->getFilePath(self::ATTR_TENTATIVES_CONTEXT);
                $hLock = $this->lock($connecteurConfig);
                try {
                    if (file_exists($fContext)) {
                        unlink($fContext);
                    }
                } catch (Exception $ex) {
                    $this->unlock($hLock);
                    throw $ex;
                }
                $this->unlock($hLock);
            }
        }
    }

    public function onAccesEchec(ConnecteurSuspensionIntf $connecteur, $id_e = 0, $id_u = 0) {
        $connecteurConfig = $connecteur->getConnecteurConfig();
        $suspension = $connecteurConfig->get(self::ATTR_SUSPENSION, false);
        if ($suspension) {
            return;
        }
        $fContext = $connecteurConfig->getFilePath(self::ATTR_TENTATIVES_CONTEXT);
        // Verrouiller le contexte
        $hLock = $this->lock($connecteurConfig);
        try {
            // Lire le contexte des tentatives
            if (file_exists($fContext)) {
                $context = file_get_contents($fContext);
                $context = json_decode($context, true);
            } else {
                $context = false;
            }
            // Le connecteur évalue 
            $poursuivre = $connecteur->onAccesEchec($context);
            // Persister le nouveau contexte
            file_put_contents($fContext, json_encode($context));
            // Suspend les accès si demandé
            if (!$poursuivre) {
                $this->setSuspension($connecteur, true);
            }
        } catch (Exception $ex) {
            $this->unlock($hLock);
            throw $ex;
        }
        // Déverrouiller le contexte
        $this->unlock($hLock);
    }

    public function onAccesSucces(ConnecteurSuspensionIntf $connecteur) {
        $this->setSuspension($connecteur, false);
    }

    private function lock(DonneesFormulaire $connecteurConfig) {
        $fLock = $connecteurConfig->getFilePath(self::ATTR_TENTATIVES_CONTEXT . '-lock');
        $hLock = fopen($fLock, 'c');
        flock($hLock, LOCK_EX);
        return $hLock;
    }

    private function unlock($hLock) {
        flock($hLock, LOCK_UN);
        fclose($hLock);
    }

}

