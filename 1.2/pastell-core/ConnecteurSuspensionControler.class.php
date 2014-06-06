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

    public function setSuspension(ConnecteurSuspensionIntf $connecteur, $suspension, $doLock = true) {
        $connecteurConfig = $connecteur->getConnecteurConfig();
        $oldSuspension = $connecteurConfig->get(self::ATTR_SUSPENSION, false);
        $fContext = $connecteurConfig->getFilePath(self::ATTR_TENTATIVES_CONTEXT);
        if ($doLock) {
            $hLock = $this->lock($connecteurConfig);
        }
        try {
            if ($oldSuspension != $suspension) {
                if ($suspension) {
                    $connecteurConfig->addFileFromData(self::ATTR_SUSPENSION, 'suspension_erreur_detail', $suspension);
                } else {
                    $connecteurConfig->removeFile(self::ATTR_SUSPENSION);
                }
            }
            if (!$suspension) {
                // Réinitialiser le contexte des tentatives
                if (file_exists($fContext)) {
                    unlink($fContext);
                }
            }
        } catch (Exception $ex) {
            if ($doLock) {
                $this->unlock($hLock);
            }
            throw $ex;
        }
        if ($doLock) {
            $this->unlock($hLock);
        }
    }

    public function onAccesEchec(ConnecteurSuspensionIntf $connecteur, Exception $accesException) {
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
                $suspensionDetail = exceptionToJson($accesException);
                $this->setSuspension($connecteur, $suspensionDetail, false);
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
