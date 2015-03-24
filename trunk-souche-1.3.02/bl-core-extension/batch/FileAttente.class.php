<?php
require_once("BLBatch.class.php");
/**
 * Classe utilitaire pour le lancement des actions automatiques par file d'attente
 * - centralisation des constantes et conventions
 * - factorisation des méthodes
 * 
 * Fichier flags :
 * - C'est un fichier au format json
 * - l'attribut pid permet de savoir si la file d'attente est déjà en cours d'exécution.
 * - L'attribut derniere_execution contient la date de début de la dernière exécution du script.
 * La date contenue dans le fichier est mise à jour à chaque lancement de la file.
 * - La date de modification du fichier est mise à jour à chaque traitement unitaire de la file (action sur document, action sur connecteur...)
 * Ce fichier permet :
 *   - de ne pas relancer la file tant que le delai_attente n'est pas respecté (en prenant en compte la date contenu dans le fichier)
 *   - de savoir si la file d'attente n'est pas arrêtée (en prenant en compte la date de modification du fichier)
 * 
 * Il est possible d'arrêter une file et empêcher son lancement en posant dans le répertoire /tmp/ un fichier nommé : batch_{file_attente_name}.stop
 *   
 */
class FileAttente {

    private $file_attente_name;
    private $objectInstancier;
    private $duree_attente;
    private $file_encours;
        
    private $conf_file_attente;
            
    private $cache_file_attente;
    private $cache_server_name;
    private $all_flux_connecteur_action;
    private $flag_path;
                       
    const LOG_FILENAME_PREFIX = 'bl-action-auto-';
        
    public static function getAllFileAttente() {
        $list_file_attente = json_decode(file_get_contents(FILE_ATTENTE_LIST_FILEPATH), true);
        return $list_file_attente;
    }

    public static function getAllConfFileAttente() {
        $conf_file_attente = json_decode(file_get_contents(FILE_ATTENTE_CONF_FILEPATH), true);
        return $conf_file_attente;
    }
        
    public function __construct($objectInstancier, $flag_path, $file_attente_name, $duree_attente) {
        $this->file_attente_name = $file_attente_name;
        $this->objectInstancier = $objectInstancier;
        $this->duree_attente = $duree_attente;
        $this->cache_server_name=array();
        $this->cache_file_attente=array();
        $this->file_encours = false;
        $this->flag_path = $flag_path;
        // Chargement du fichier contenant les associations entre action et type de connecteur
        $this->all_flux_connecteur_action = json_decode(file_get_contents('/var/pastell_settings/connecteur_action.json'), true);        
    }

    public function __destruct() {
        // Suppression du fichier flag en cas d'arret pour maintenance de l'application ou arret des batchs
        if ($this->file_encours) {            
            $this->arreterExecutionFileAttente();
        }
    }

    private function getFileAttenteInfoFilePath() {
        return $this->flag_path . "/" . $this->file_attente_name;
    }
    
    public function demarrerExecutionFileAttente() {
        // Création du fichier d'info sur la file d'attente contenant :
        // PID : pid du processus de la file d'attente
        // derniere_execution : date de lancement de la dernière exécution de la file d'attente
        $this->file_encours = true;
        $info = array('pid' => getmypid(), 'derniere_execution' => date('Y-m-d H:i:s'));
        $this->saveInfosFileAttente($info);
    }
        
    private function saveInfosFileAttente($info) {        
        file_put_contents($this->getFileAttenteInfoFilePath(), json_encode($info));
    }
    
    private function getInfosFileAttente() {
		if (!file_exists($this->getFileAttenteInfoFilePath())){
			return array('pid'=>false,'derniere_execution'=>false);
		}
		$content_json = file_get_contents($this->getFileAttenteInfoFilePath());
		if (!$content_json){
			return array('pid'=>false,'derniere_execution'=>false);
		}        
		return json_decode($content_json, true);        
    }
    
    public function arreterExecutionFileAttente() {
        $info = $this->getInfosFileAttente();
        $info['pid'] = false;
        $this->saveInfosFileAttente($info);
        $this->file_encours = false;
    }
    
    public function isFileAttenteEnCoursTraitement() {
        $info = $this->getInfosFileAttente();
        return !($info['pid']===false);                    
    }
    
    public function majMtime() {
        touch($this->getFileAttenteInfoFilePath());
    }
        
    public function getLastMtime() {
        if (file_exists($this->getFileAttenteInfoFilePath())) {
            return date("Y-m-d H:i:s", filemtime($this->getFileAttenteInfoFilePath()));
        } else {
            return false;
        }
    }
    
    // Return true si : 
    //  - le fichier flag n'existe pas
    //  - si la durée est supérieure à la duree d'attente de la file.
    public function isFileAttenteWarning() {
		if (! file_exists($this->getFileAttenteInfoFilePath())) {
			return true;
		}
		return (time() - filemtime($this->getFileAttenteInfoFilePath())) >= $this->duree_attente;        
    }
    
    public function getDureeLastMTime() {
        if (! file_exists($this->getFileAttenteInfoFilePath())) {
			return false;
		}
		return (time() - filemtime($this->getFileAttenteInfoFilePath()));
    }
    
    public function getDateDerniereExecution() {
        $info = $this->getInfosFileAttente();
        return $info['derniere_execution'];
    }
            
    private function getServerNameConnecteur($id_ce) {
        // Pour éviter de charger la classe du connecteur à chaque fois, la relation id_ce - server_name est stockée en cache
        foreach($this->cache_server_name as $id_ce_server_name) {
            if ($id_ce_server_name['id_ce'] == $id_ce) {
                return $id_ce_server_name['server_name'];
            }
        }
        // Récupération du nom du serveur via une méthode implémentée dans chaque connecteur.
        $connecteur = $this->objectInstancier->ConnecteurFactory->getConnecteurById($id_ce);
        $server_name = $connecteur->getServerName();
        
        // Mise en cache de la valeur trouvée
        $this->cache_server_name[] = array('id_ce' => $id_ce, 'server_name' => $server_name);
        
        return $server_name;
    }
        
    private function getFileAttenteFromConf($server_name, $action) {
        // Il faut trouver le sous-ensemble répondant au server_name
        // Parmi le sous-ensemble, il faut trouver la file contenant l'action concernée
        // Si elle n'est pas trouvée il faut prendre la file contenant "*" dans action.         
        $file_attente_conf = $this->getConfFileAttente();
        $file_trouve = false;
        foreach($file_attente_conf as $file_conf) {
            if (strcasecmp($server_name, $file_conf['server_name'])===0) {
                $actions_conf = $file_conf['action'];
                if (is_array($actions_conf)) {
                    foreach($actions_conf as $action_conf) {
                        if ($action_conf == "*") {
                            // Je le garde mais il faut continuer à chercher l'action attendue dans le cas ou elle serait sur une autre file.                            
                            $file_trouve = $file_conf['file'];
                        } else if ($action_conf == $action) {
                            $file_trouve = $file_conf['file'];
                            break;
                        }
                    }
                } else {
                    if ($actions_conf == "*") {
                        // Je le garde mais il faut continuer à chercher l'action attendue dans le cas ou elle serait sur une autre file.
                        $file_trouve = $file_conf['file'];
                    } else if ($actions_conf == $action){
                        $file_trouve = $file_conf['file'];
                        break;
                    }
                }                
            }
        }
        return $file_trouve;
    }
    
    public function getTypeConnecteur($type_flux, $action) {
        $all_connecteur_action = array();
        foreach ($this->all_flux_connecteur_action as $flux) {
            if ($flux['flux']==$type_flux) {
                $all_connecteur_action = $flux['connecteur_action'];                
                break;
            }
        }
        if ($all_connecteur_action) {
            foreach($all_connecteur_action as $connecteur_action) {
                if ($connecteur_action['action'] == $action) {
                    return $connecteur_action['type_connecteur'];
                }
            }
        }
        return false;
    }
      
    private function determinerFileAttente($id_ce, $action) {
        $server_name= $this->getServerNameConnecteur($id_ce);
        if (!$server_name) {
             return false;
        }
        $file_trouve = $this->getFileAttenteFromConf($server_name, $action);
       
        return $file_trouve;
    }
    
    public function isTraitementExecutableSurFLux($type_flux, $id_e, $action) {
        
        // Récupération de la file d'attente dans le cache        
        $file_trouve = $this->getFileAttenteFromCache($type_flux, $id_e, '', $action);
        if ($file_trouve === false) {
            // Si pas trouvé, il faut la déterminer
            $type_connecteur = $this->getTypeConnecteur($type_flux, $action);
            $file_from_conf = false;
            if ($type_connecteur) {
                $connecteur = $this->objectInstancier->FluxEntiteSQL->getConnecteur($id_e, $type_flux, $type_connecteur);
                if ($connecteur) {
                    $file_from_conf = $this->determinerFileAttente($connecteur['id_ce'], $action);
                }
            }
            // Si pas trouvé en conf, le traitement sera exécuté dans la file DEFAULT.
            $file_trouve = $file_from_conf? $file_from_conf : self::FILE_ATTENTE_DEFAUT;
            
            // stocker la file d'attente dans le cache.
            $this->cache_file_attente[] = array('type_flux' => $type_flux, 'id_e' => $id_e, 'id_ce' => '', 'action' => $action, 'file' => $file_trouve);
        } 
               
        // Déternminer si le traitement est executable sur la file d'attente
        if ($this->file_attente_name == $file_trouve) {
            return true;
        }
        return false;
    }
    
    public function isTraitementExecutableSurConnecteur($id_ce, $action) {
        
        // Récupération de la file d'attente dans le cache        
        $file_trouve = $this->getFileAttenteFromCache('', '', $id_ce, $action);
        if ($file_trouve === false) {
            // Si pas trouvé, il faut la déterminer                                    
            $file_from_conf = $this->determinerFileAttente($id_ce, $action);                
            
            // Si pas trouvé en conf, le traitement sera exécuté dans la file DEFAULT.
            $file_trouve = $file_from_conf? $file_from_conf : self::FILE_ATTENTE_DEFAUT;
            
            // stocker la file d'attente dans le cache.
            $this->cache_file_attente[] = array('type_flux' => '', 'id_e' => '', 'id_ce' => $id_ce, 'action' => $action, 'file' => $file_trouve);
        } 
               
        // Déternminer si le traitement est executable sur la file d'attente
        if ($this->file_attente_name == file_trouve) {
            return true;
        }
        return false;
    }    
    
    // Le cache de file d'attente est un tableau associatif : 'type_flux', 'id_e', 'id_ce', 'action', 'file'
    // Pour les actions sur document, l'attribut 'id_ce' n'est pas renseigné.
    // Pour les actions sur connecteur, les attributs 'type_flux', 'id_e' ne sont pas renseignés.
    private function getFileAttenteFromCache($type_flux, $id_e, $id_ce, $action) {
        foreach($this->cache_file_attente as $cache) {
            if ($id_ce) {
                // File attente pour les actions de connecteur
                if ($cache['id_ce'] == $type_flux && $cache['action'] == $action) {
                    return $cache['file'];
                }
            } else {
                // File attente pour les documents
                if ($cache['type_flux'] == $type_flux && $cache['id_e'] == $id_e && $cache['action'] == $action) {
                    return $cache['file'];
                }
            }
        }
        return false;
    }
        
    public function getFileAttenteName() {
        return $this->file_attente_name;
    }
    
    public function getDureeAttente() {
        return $this->duree_attente;
    }
    
    public function getLogFileName() {
        return self::LOG_FILENAME_PREFIX . $this->file_attente_name . ".log";
    }      
    
    private function getConfFileAttente() {
        if (!$this->conf_file_attente) {
            $this->conf_file_attente = self::getAllConfFileAttente();
        }
        return $this->conf_file_attente;
    }
    
    public function checkFileAttenteStop() {
        $files = $this->isFileAttenteStop();
        if ($files) {
            $blscript = new BLBatch();
            $blscript->displayBatchStopAndDie();
        }
    }
    
    public function isFileAttenteStop() {
        $files = glob('/tmp/batch_' . $this->file_attente_name . '.stop');
        return $files;
    }
    
}
