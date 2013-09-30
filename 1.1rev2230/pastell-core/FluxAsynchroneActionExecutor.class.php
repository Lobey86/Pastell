<?php

require_once(PASTELL_PATH . "/pastell-core/FluxSynchroneActionExecutor.class.php");

abstract class FluxAsynchroneActionExecutor extends FluxSynchroneActionExecutor {
    const ACTION_SYNCHRONE_DEFAUT = false;
    
    private $synchroneActionName;

    public function __construct(ObjectInstancier $objectInstancier, $synchroneActionName) {
        parent::__construct($objectInstancier);        
        $this->synchroneActionName = $synchroneActionName;
    }

    public function go() {
        try {
            if ($this->synchroneActionName === self::ACTION_SYNCHRONE_DEFAUT) {
                $this->synchroneActionName = substr($this->action, 0, strlen($this->action) - strlen('-demande'));
            }
            $syncGoRet = $this->objectInstancier->ActionExecutorFactory->executeOnDocumentThrow(
                    $this->id_d, 
                    $this->id_e, 
                    $this->id_u, 
                    $this->synchroneActionName, 
                    $this->id_destinataire,
                    $this->from_api,
                    $this->action_params);
            $this->setLastMessage($this->objectInstancier->ActionExecutorFactory->getLastMessage());
            return $syncGoRet;
        } catch (ConnecteurActivationException $gofEx) {
            // Action synchrone reportée; sera déclenchée par action automatique.
            return parent::go();
        } catch (ConnecteurAccesException $gofEx) {
            // Action synchrone reportée; sera déclenchée par action automatique.
            return parent::go();
        } catch (Exception $gofEx) {
            // Action synchrone en erreur fonctionnelle. 
            throw $gofEx;
        }
        parent::go();
    }

    protected function goFonctionnel() {
        return array(
            self::GO_KEY_ETAT => self::GO_ETAT_OK,
            self::GO_KEY_MESSAGE => self::GO_MESSAGE_ACTION);
    }

}