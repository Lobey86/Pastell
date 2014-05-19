<?php

//Note : SoapClient crée un fichier de cache du WSDL, voir http://www.php.net/manual/en/soap.configuration.php

class SoapClientFactory {

	public function getInstance($wsdl,array $options = array(),$is_jax_ws = false){
		return new NotBuggySoapClient($wsdl, $options,$is_jax_ws);
    }

}

$soapErrorException = null;

function soapErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
    global $soapErrorException;
    $soapErrorException = soapErrorAdd($errstr, $errno);
    return false;
}

function soapErrorAdd($errstr, $errno = 0) {
    global $soapErrorException;
    $cause = $soapErrorException;
    $error = $errstr;
    if ($errno != 0) {
        $error = '(' . $errno . ') ' . $error;
    }
    if ($cause) {
        $error .= " - Cause : " . $cause;
    }
    return $error;
}

class NotBuggySoapClient extends SoapClient {

    private $is_jax_ws;
    private $option;

//PHP SUCKS : https://bugs.php.net/bug.php?id=47584	
	public function __construct($wsdl,array $options = array(),$is_jax_ws = false){		
        global $soapErrorException;
        $this->is_jax_ws = $is_jax_ws;
        $this->option = $options;
        $options['exceptions'] = 1;
        $options['trace'] = 1;
        if (function_exists('xdebug_disable')) {
            xdebug_disable();
        }
        $soapErrorException = null;
        $errorHandlerSave = set_error_handler('soapErrorHandler');
        try {
            parent::__construct($wsdl, $options);
        } catch (SoapFault $soapFault) {
            $this->construct_finally($errorHandlerSave);
            $exEtCauses = soapErrorAdd($soapFault->getMessage());
            throw new SoapFault($soapFault->faultcode, $exEtCauses);
        } catch (Exception $ex) {
            $this->construct_finally($errorHandlerSave);
            $exEtCauses = soapErrorAdd($ex->getMessage());
            throw new Exception($exEtCauses, $ex->getCode(), $ex);
        }
        $this->construct_finally($errorHandlerSave);
    }

    private function construct_finally($errorHandlerSave) {
        restore_error_handler($errorHandlerSave);
        if (function_exists('xdebug_enable')) {
            xdebug_enable();
        }
    }

//http://stackoverflow.com/questions/5948402/having-issues-with-mime-headers-when-consuming-jax-ws-using-php-soap-client	
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
		if (isset($this->option['use_curl'])){
            $response = $this->doRequestWithCurl($request, $location, $action, $version);
        } else {
            global $soapErrorException;
            $soapErrorException = null;
            $errorHandlerSave = set_error_handler('soapErrorHandler');
            try {
                $response = parent::__doRequest($request, $location, $action, $version, $one_way);
            } catch (SoapFault $soapFault) {
                $this->doRequest_finally($errorHandlerSave);
                $exEtCauses = soapErrorAdd($soapFault->getMessage());
                throw new SoapFault($soapFault->faultcode, $exEtCauses);
            } catch (Exception $ex) {
                $this->doRequest_finally($errorHandlerSave);
                $exEtCauses = soapErrorAdd($ex->getMessage());
                throw new Exception($exEtCauses, $ex->getCode(), $ex);
            }
            $this->doRequest_finally($errorHandlerSave);
            if (isset($this->__soap_fault) && ($this->__soap_fault != null)) {
                //this is where the exception from __doRequest is stored 
                $exEtCauses = soapErrorAdd($this->__soap_fault->getMessage());
                throw new SoapFault($this->__soap_fault->faultcode, $exEtCauses);
            }
        }

        // Analyse de la response pour la formater en XML valide
        // Pour le format multipart XOP MTOM, il faut fusionner les parts dans l'enveloppe XML.
        $headers = $this->__getLastResponseHeaders();        
        if (preg_match('/Content-Type: Multipart\/Related;.*type="application\/xop\+xml";/i', $headers) === 1) {
            $response = $this->formaterRetourMultiPartXOPToXML($response, $headers);
        } else {    	
            $pos = stripos($response, "<?xml");            
            if (!$pos) {
                $pos = stripos($response, "<soap:");
            }
            $response = substr($response, $pos);
            $pos = stripos($response, "--uuid:");
            if ($pos) {
                $response = substr($response, 0, $pos);
            }
        }

        return $response;
    }

    private function doRequest_finally($errorHandlerSave) {
        restore_error_handler($errorHandlerSave);
    }

    public function doRequestWithCurl($request, $location, $action, $version)
    {
        $headers = array
            (
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
        );

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch,  CURLOPT_SSL_VERIFYHOST , false ); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if (isset( $this->option['userCertOnly'])){
            curl_setopt($ch, CURLOPT_SSLCERT, $this->option['userCertOnly']);
            curl_setopt($ch, CURLOPT_SSLKEY, $this->option['userKeyOnly']);
			curl_setopt($ch, CURLOPT_SSLKEYPASSWD,$this->option['passphrase'] );
        }


        if ($this->option['login'])
        {
            curl_setopt($ch, CURLOPT_HTTPAUTH,  CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->option['login'].':'.$this->option['password']);
        }
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ch);
        if (curl_errno($ch) !== 0)
        {
            throw new Exception('CurlSoapClient, curl error ('.curl_errno($ch).'): ' .
            curl_error($ch));
        }

        $this->__last_request_headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $this->__last_request = $request;

        return $response;
    }

    private function agregerPartsToEnveloppe($content_enveloppe, $parts) {
        foreach($parts as $mpart) {
            if ($mpart->isEnv==false) {
                $content_part = $mpart->content;
                $content_id = $mpart->header['content-id'];
                //Suppression des <> en fin et début du content_id
                $content_id = substr($content_id,1,-1);
                $content_enveloppe = $this->remplacerRefParContentPart($content_enveloppe, $content_id, $content_part);
            }
        }
        return $content_enveloppe;
    }
    
    private function remplacerRefParContentPart($content_enveloppe, $contentId, $content_part) {
        // Recherche du contentId
        $matches=array();
        if (preg_match('/(<xop:.*'. $contentId . '.*\/>)/i', $content_enveloppe, $matches)===1) {
            $content_part64 = base64_encode($content_part);
            $content_enveloppe = str_replace($matches[1], $content_part64, $content_enveloppe);
        }
        return $content_enveloppe;
    }
    
    private function formaterRetourMultiPartXOPToXML($response, $headers) {
        $boundary = array();
        $start = array();
        $multiParts = array();
        $CRLF  = "\r\n";
        // Boundary hyphens
        $BHYP  = "--";
        
        if (preg_match('/boundary="(.*)"/Ui', $headers, $boundary) === 1 && preg_match('/start="(.*)"/Ui', $headers, $start) === 1) {

            $parts = explode($CRLF . $BHYP . $boundary[1], $response);

            if (isset($parts[0]) && empty($parts[0]))
                array_shift($parts);

            foreach ($parts as $part) {
                // Is it over?
                if (preg_match("/" . $BHYP . "$/i", $part))
                    break;

                // New part
                $multiPart = new multiPart();
                

                // start position
                $startp = $part[0] . $part[1] === $CRLF ? 2 : 0;
                // Headers end position
                $h_endp = strpos($part, $CRLF . $CRLF, 0);

                // Actual part's header string line by line
                foreach (explode($CRLF, substr($part, $startp, $h_endp)) as $h_line) {
                    $multiPart->header[strtolower(strstr($h_line, ': ', TRUE))] = substr(strstr($h_line, ': '), 2);
                }

                // This is the envelope, so set the response
                if ($multiPart->header['content-id'] === $start[1]) {
                    $multiPart->isEnv = TRUE;
                    $multiPart->content = substr($part, $h_endp + 4);
                }
                // Its not the soap envelope
                else {
                    // Get actual part's content
                    switch ($multiPart->header['content-transfer-encoding']) {
                        case 'base64': {
                                $multiPart->content = base64_decode(substr($part, $h_endp + 4));
                            }
                            break;

                        case 'binary':
                        default: {
                                $multiPart->content = substr($part, $h_endp + 4);
                            }
                            break;
                    }
                }
                $multiParts[] = $multiPart;
            }

            // Insertion des parts dans l'enveloppe principale                
            $parts = array();
            foreach ($multiParts as $mpart) {
                if ($mpart->isEnv == true) {
                    $content_enveloppe = $mpart->content;
                } else {
                    $parts[] = $mpart;
                }
            }

            $result = $this->agregerPartsToEnveloppe($content_enveloppe, $parts);

            return $result;
        }
    }

}



class multiPart {
    public $header      = array();
    public $content     = '';
    public $isEnv       = FALSE;
}
