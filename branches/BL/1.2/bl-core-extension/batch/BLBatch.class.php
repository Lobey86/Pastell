<?php

/**
 * Classe d'utilitaires dédiés à l'exécution de scripts batch.
 * - formattage d'affichage selon mode d'appel
 * - détection d'interruption, pour les cas de batch longs
 * - horodatage
 * - mesures de temps d'exécution et de mémoire consommée
 * - alimentation d'une todolist, tracée en terminaison de script
 * 
 * La todolist est générée en terminaison de script. Elle est soit ajoutée dans 
 * le fichier défini par le paramètre de nom self::TODOLIST_FILEPATH, soit affichée
 * si ce paramètre n'est pas défini.
 */
class BLBatch {

    const ATTR_TODOLIST_FILEPATH = 'todolist_filepath';

    private $todoList_filepath;
    private $todoList;

    public function __construct() {
        if (PHP_SAPI != 'cli') {
            header("Content-type: text/html; charset=iso-8859-15");
        }
        $this->todoList_filepath = $this->getArg(self::ATTR_TODOLIST_FILEPATH, false);
        $this->todoList = array();
    }

    public function __destruct() {
        if ($this->todoList) {
            if (!$this->todoList_filepath) {
                $this->traceln('');
                $this->traceln('====> Paramétrages complémentaires à effectuer :');
            }
            foreach ($this->todoList as $todo) {
                $this->traceln($todo, true, $this->todoList_filepath);
            }
            if (!$this->todoList_filepath) {
                $this->traceln('');
            }
        }
        $this->todoList_count = 0;
        unset($this->todoList);
    }

    public function trace($texte, $utf8 = true, $toFile = false) {
        if ($toFile || ($utf8 && (PHP_SAPI == 'cli'))) {
            $texte = utf8_encode($texte);
        }
        if ($toFile) {
            throwIfFalse(file_put_contents($toFile, $texte, FILE_APPEND));
        } else {
            echo $texte;
        }
    }

    public function traceln($texte = '', $utf8 = true, $toFile = false) {
        $this->trace($texte . (PHP_SAPI == 'cli' ? "\n" : "<BR/>"), $utf8, $toFile);
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

    public function addTodo($texte) {
        $this->todoList[] = $texte;
    }

}
