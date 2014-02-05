<?php

require_once(PASTELL_PATH . "/lib/SoapClientFactory.class.php");

class ConnecteurSuspensionSoapClient extends NotBuggySoapClient {

    private $objectInstancier;
    private $connecteur;

    public function __construct(ObjectInstancier $objectInstancier, $connecteur, $wsdl, array $options = array(), $is_jax_ws = false) {
        parent::__construct($wsdl, $options, $is_jax_ws);
        $this->objectInstancier = $objectInstancier;
        $this->connecteur = $connecteur;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        try {
            $result = parent::__doRequest($request, $location, $action, $version, $one_way);
            $this->objectInstancier->ConnecteurSuspensionControler->onAccesSucces($this->connecteur);
            return $result;
        } catch (SoapFault $soapFault) {
            $this->checkSoapFault($soapFault);
        }
    }

    // Déclarée deprecated mais appelée quand même par la classe SoapClient, notamment pour contrôler la fonction avant le doRequest.
    public function __call($function_name, $arguments) {
        try {
            $result = parent::__call($function_name, $arguments);
            return $result;
        } catch (SoapFault $soapFault) {
            $this->checkSoapFault($soapFault);
        }
    }

    private function checkSoapFault(SoapFault $soapFault) {
        $message = $soapFault->getMessage();
        if (
                (preg_match('/Could not connect to host/is', $message) === 1) ||
                (preg_match('/Forbidden/is', $message) === 1) ||
                (preg_match('/Service Temporarily Unavailable/is', $message) === 1) ||
                (preg_match('/not a valid method for this service/is', $message) === 1)) {
            throw new ConnecteurAccesException($this->connecteur, $soapFault->getMessage());
        }
        throw $soapFault;
    }

}
