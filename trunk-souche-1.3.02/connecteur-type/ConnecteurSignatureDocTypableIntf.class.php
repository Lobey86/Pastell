<?php

/**
 * Interface décrivant un connecteur de signature prenant en charge 
 * le typage (type, circuit) au niveau du document.
 */ 
interface ConnecteurSignatureDocTypableIntf {

    const CIRCUIT_ACTION_VISA = 'visa';
    const CIRCUIT_ACTION_SIGNATURE = 'signature';
    const CIRCUIT_ACTION_INCONNUE = 'inconnue';
    
    const KEY_CIRCUIT_ACTION = 'action';
    const KEY_CIRCUIT_ACTEUR = 'acteur';
    
    /**
     * Retourne la liste des types.
     * </p>
     * Les applications métiers doivent considérer cette information comme une 
     * option de configuration, dont la fréquence de modification est faible. 
     * A ce titre, les types ne nécessitent pas de mise en cache.
     * @return array(int => string)
     */
    public function getListTypes();

    /**
     * Retourne la liste des circuits d'un type
     * </p>
     * Les applications métiers doivent pouvoir accéder à ces informations
     * même lorsque le parapheur cible n'est pas accessible. Il est donc 
     * préférable d'obtenir les circuits à partir d'un cache local (voir
     * @link ConnecteurSignatureDocTypableIntf::cacherListCircuits).
     * @return array(int => string)
     */
    public function getListCircuits($type);
    
    /**
     * Met en cache les circuits de la collectivité.
     * </p>
     * Les applications métiers doivent pouvoir accéder aux circuits même 
     * lorsque le parapheur cible n'est pas accessible. Il est donc 
     * préférable de mettre les circuits en cache.<br/>, 
     * En associant cette opération à une action du connecteur, elle peut alors 
     * être déclenchée à fréquence régulière (par cron journalier par exemple),
     * ou ponctuellement, depuis la console par exemple.<br/>
     */
    public function cacherListCircuits();

    /**
     * Retourne le détail d'un circuit, constitué de la liste de ses étapes,
     * avec pour chacune, l'action et l'acteur.
     * @param type $type
     * @param type $circuit
     * @return array(
     *           int => array(
     *                    KEY_CIRCUIT_ACTEUR => string, 
     *                    KEY_CIRCUIT_ACTION => (string)CIRCUIT_ACTION_*
     *                  )
     *         )
     */
    public function getCircuitDetail($type, $circuit);
}
