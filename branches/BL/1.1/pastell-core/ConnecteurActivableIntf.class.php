<?php
/**
 * Interface décrivant un connecteur activable/désactivable.
 */ 
// TODO proposition de généralisation : supprimer cette interface et généraliser dans Connecteur.class
//  - attribut de classe (ex : activate)
//  - attribut de formulaire, de nom générique (ex : activate)
//  - renommer les attributs de formulaires existants (ex : iparapheur_activate)
//  - dans les classes dérivées, utiliser la méthode isActif au lieu de l'attribut
//  - activer les connecteurs ayant reçu ce nouvel attribut
interface ConnecteurActivableIntf {
    public function isActif();
}

