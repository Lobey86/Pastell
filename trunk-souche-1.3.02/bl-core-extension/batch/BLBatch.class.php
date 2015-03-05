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

    /**
     * @return PDOStatement
     */
    function sqlPrepare(SQLQuery $sqlQuery, $sql) {
        $pdo = $sqlQuery->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->pdo = $pdo;
        return $stmt;
    }

    /**
     * Exécute une requête INSERT et renvoie l'id du dernier élément ajouté.
     * @return int id du dernier élément ajouté, ou FALSE en cas d'erreur (attention à la différence entre FALSE et 0)
     */
    function sqlInsert(PDOStatement $stmt, array $params = null) {
        /** @var $pdo PDO */
        $pdo = $stmt->pdo;
        $result = $stmt->execute($params);
        $lastInsertId = $result ? $pdo->lastInsertId() : FALSE;
        $stmt->closeCursor();
        return $lastInsertId;
    }

    /**
     * Exécute une requête SELECT et renvoie les éléments
     * @return array éléments, ou FALSE en cas d'erreur
     */
    function sqlSelect(PDOStatement $stmt, array $params = null) {
        $result = $stmt->execute($params);
        $stmt->closeCursor();
        return $result;
    }

    /**
     * Exécute une requête UPDATE et renvoie le nombre de lignes modifiées.
     * @return mixed nombre de lignes modifiées, ou FALSE en cas d'erreur (attention à la différence entre FALSE et 0)
     */
    function sqlUpdate(PDOStatement $stmt, array $params = null) {
        $result = $stmt->execute($params);
        $rowCount = $result ? $stmt->rowCount() : FALSE;
        $stmt->closeCursor();
        return $rowCount;
    }

    /**
     * Exécute une requête DELETE et renvoie le nombre de lignes supprimées.
     * @return mixed nombre de lignes supprimées, ou FALSE en cas d'erreur (attention à la différence entre FALSE et 0)
     */
    function sqlDelete(PDOStatement $stmt, array $params = null) {
        $result = $stmt->execute($params);
        $rowCount = $result ? $stmt->rowCount() : FALSE;
        $stmt->closeCursor();
        return $rowCount;
    }

}
