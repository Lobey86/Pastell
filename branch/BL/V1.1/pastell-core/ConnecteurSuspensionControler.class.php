<?php

class ConnecteurSuspensionControler {

    const ATTR_TENTATIVES_CONTEXT = 'tentatives-context';
    const ATTR_SUSPENSION = 'acces_suspendu';

    public static function isSuspension(ConnecteurSuspensionIntf $connecteur) {
        $connecteurConfig = $connecteur->getConnecteurConfig();
        $suspension = $connecteurConfig->get(self::ATTR_SUSPENSION, false);
        return $suspension;
    }

    public static function setSuspension(ConnecteurSuspensionIntf $connecteur, $suspension) {
        $connecteurConfig = $connecteur->getConnecteurConfig();
        $oldSuspension = $connecteurConfig->get(self::ATTR_SUSPENSION, false);
        if ($oldSuspension != $suspension) {
            $connecteurConfig->setData(self::ATTR_SUSPENSION, $suspension);
            if (!$suspension) {
                // Réinitialiser le contexte des tentatives
                $fContext = $connecteurConfig->getFilePath(self::ATTR_TENTATIVES_CONTEXT);
                $hLock = self::lock($connecteurConfig);
                try {
                    if (file_exists($fContext)) {
                        unlink($fContext);
                    }
                } catch (Exception $ex) {
                    self::unlock($hLock);
                    throw $ex;
                }
                self::unlock($hLock);
            }
        }
    }

    public static function onAccesEchec(ConnecteurSuspensionIntf $connecteur) {
        $connecteurConfig = $connecteur->getConnecteurConfig();
        $suspension = $connecteurConfig->get(self::ATTR_SUSPENSION, false);
        if ($suspension) {
            return;
        }
        $fContext = $connecteurConfig->getFilePath(self::ATTR_TENTATIVES_CONTEXT);
        // Verrouiller le contexte
        $hLock = self::lock($connecteurConfig);
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
                self::setSuspension($connecteur, true);
            }
        } catch (Exception $ex) {
            self::unlock($hLock);
            throw $ex;
        }
        // Déverrouiller le contexte
        self::unlock($hLock);
    }

    public static function onAccesSucces(ConnecteurSuspensionIntf $connecteur) {
        self::setSuspension($connecteur, false);
    }

    private static function lock(DonneesFormulaire $connecteurConfig) {
        $fLock = $connecteurConfig->getFilePath(self::ATTR_TENTATIVES_CONTEXT . '-lock');
        $hLock = fopen($fLock, 'c');
        flock($hLock, LOCK_EX);
        return $hLock;
    }

    private static function unlock($hLock) {
        flock($hLock, LOCK_UN);
        fclose($hLock);
    }

}

