<?php

/**
 * Classe d'utilitaires dédiés à l'exécution de scripts batch.
 * - formattage d'affichage selon mode d'appel
 * - détection d'interruption, pour les cas de batch longs
 * - horodatage
 */
class BLBatch {

    public function __construct() {
        if (PHP_SAPI != 'cli') {
            header("Content-type: text/html; charset=iso-8859-15");
        }
    }

    public function trace($texte, $encode = true) {
        if (PHP_SAPI == 'cli') {
            if ($encode) {
                $texte = utf8_encode($texte);
            }
        }
        echo $texte;
    }

    public function traceln($texte = '', $encode = true) {
        $this->trace($texte, $encode);
        if (PHP_SAPI == 'cli') {
            echo "\n";
        } else {
            echo "<BR/>";
        }
    }

    public function heure() {
        return '[' . date('d/m/Y H:i:s') . ']';
    }

    public function isBatchStop() {
        $files = glob('/tmp/batch.stop');
        return $files;
    }

    public function displayBatchStopAndDie() {
        $this->traceln($this->heure() . " Script batch interrompu par fichier flag.");
        die(1);
    }

    public function checkBatchStop() {
        if ($this->isBatchStop()) {
            $this->displayBatchStopAndDie();
        }
        if (isAppLocked()) {
            displayAppLockedAndDie();
        }
    }

    public function read($prompt, $default = null) {
        echo utf8_encode($prompt . ' : ');
        $ret = utf8_decode(trim(fgets(STDIN)));
        if (empty($ret)) {
            if (isset($default)) {
                return $default;
            }
            $this->error('Abandon');
            exit(1);
        }
        return $ret;
    }

    private function error($text) {
        echo utf8_encode($text . "\n");
        exit(1);
    }

    /**
     * En mode http comme en mode 'cli', les paramètres sont fournis au format {name}={value}.
     * @param string $name
     * @param mixed $default donne la valeur de l'argument si l'argument n'est pas déclaré.<br>
     *      L'argument est facultatif quand une valeur par défaut est fournie (non null), 
     *      obligatoire sinon.
     * @return mixed
     */
    public function getArg($name, $default = null) {
        global $argc;
        global $argv;

        if (PHP_SAPI == 'cli') {
            for ($iarg = 1; $iarg < count($argv); $iarg++) {
                $argNameValue = explode('=', $argv[$iarg]);
                $argName = $argNameValue[0];
                if ($argName == $name) {
                    if (count($argNameValue) >= 2) {
                        $argValue = $argNameValue[1];
                    } else {
                        $argValue = null;
                    }
                    break;
                }
            }
        } else {
            $recuperateur = new Recuperateur($_GET);
            $argValue = $recuperateur->get($name, $default);
        }

        if (empty($argValue)) {
            if (isset($default)) {
                return $default;
            }
            throw new Exception('Paramètre \'' . $name . '\' non fourni');
        }

        return $argValue;
    }

    /**
     * Exécute une fonction en mesurant sa durée et la mémoire consommée.
     * @param Closure $function fonction exécutant le traitement à mesurer. Aucun paramètre.
     * @return array tableau à indexation numérique contenant le résultat de la fonction, la durée, la mémoire consommée
     */
    function mesurer($function) {
        $debut = microtime(true);
        $mem = memory_get_usage(true);
        $result = $function();
        $duree = round(microtime(true) - $debut, 3);
        $mem = memory_get_usage(true) - $mem;
        return array($result, $duree, $mem);
    }

}
