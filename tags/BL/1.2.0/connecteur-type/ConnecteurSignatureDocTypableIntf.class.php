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
     * Retourne la liste des types
     * @return array(int => string)
     */
    public function getListTypes();

    /**
     * Retourne la liste des circuits d'un type
     * @return array(int => string)
     */
    public function getListCircuits($type);
    
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
