<?php

/**
 * Interface décrivant un connecteur prenant en charge la suspension automatique
 * d'accès au service.
 */
// TODO proposition de généralisation : supprimer cette interface et généraliser dans Connecteur.class.
// Les méthodes ne seraient pas abstraites et fourniraient un comportement par défaut
// compatible avec l'ancien fonctionnement : pas de limite de tentatives.
interface ConnecteurSuspensionIntf {

    /**
     * Retourne les données du connecteur
     * @return DonneesFormulaire
     */
    public function getConnecteurConfig();

    /**
     * Evénement déclenché lorsqu'une tentative d'accès a échoué.
     * @param array $tentativesContext contexte de calcul des tentatives
     *      En entrée
     *          Le contexte reprend le calcul effectué lors du précédent échec.
     *          Un contexte indéfini (false) signale qu'aucun cas d'échec n'a 
     *          précédé, ou que le contexte été réinitialisé (reprise après suspension).
     *      En sortie
     *          Le contexte peut être modifié; il sera persisté et fourni lors 
     *          du prochain appel.
     * @return true : les tentatives peuvent se poursuivre
     *         false : la limite est atteinte; les accès seront suspendus
     */
    public function onAccesEchec(&$tentativesContext);
}

