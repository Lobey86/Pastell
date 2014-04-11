<?php

class BLCreationUtilisateur {
    
    private $id_e;
    private $denominationEntite;
    private $email;
    private $login;
    private $password;    
    private $nom;
    private $prenom;   
    private $role;

    private $blBatch;
    
    public function __construct($blBatch) {
        $this->blBatch = $blBatch;
    }
    
    public function creerUtilisateur() {
        $this->demanderSaisieInformationUtilisateur();
        if ($this->controlerInformationUtilisateur()) {
            global $objectInstancier;
            $id_u = $objectInstancier->UtilisateurControler->editionUtilisateur($this->id_e,null,$this->email,$this->login,$this->password,$this->password,$this->nom,$this->prenom,null);            
            $objectInstancier->RoleUtilisateur->addRole($id_u,$this->role,$this->id_e);
            $this->blBatch->traceln("Création de l'utilisateur $this->login : OK");
            return $id_u;
        } else {
            throw new Exception ("Les informations saisies sont incorrectes.");
        }
        
    }

    public function creerAdmin() {
        $this->demanderSaisieInformationAdmin();
        if ($this->controlerInformationAdmin()) {
            global $objectInstancier;
            $result = $objectInstancier->AdminControler->createAdmin($this->login,$this->password,$this->email);
            if ($result){
                $this->blBatch->traceln("Création de l'administrateur $this->login : OK");	
            } else {
                $this->traceln($objectInstancier->AdminControler->getLastError());
                exit;
            }
            $objectInstancier->AdminControler->fixDroit();
        } else {
            throw new Exception ("Les informations saisies sont incorrectes.");
        }
    }
    
    private function controlerInformationUtilisateur() {
        return true;
    }
    
    private function controlerInformationAdmin() {
        return true;
    }
    
    private function demanderSaisieInformationUtilisateur() {
        if (!isset($this->id_e)) {
            $this->id_e= $this->blBatch->read('id_e');
        }
        if (!isset($this->email)) {
            $this->email= $this->blBatch->read('adresse mail');
        }
        if (!isset($this->login)) {
            $this->login= $this->blBatch->read('login');
        }        
        if (!isset($this->password)) {
            $this->password= $this->blBatch->read('password');            
        }
        
        if (!isset($this->nom)) {
            $this->nom= $this->blBatch->read('nom');
        }
        if (!isset($this->prenom)) {
            $this->prenom= $this->blBatch->read('prenom');
        }
        if (!isset($this->role)) {
            $this->role= $this->blBatch->read('role (adminEntite, apiDocument, apistat)');
        }                
    }
    
    private function demanderSaisieInformationAdmin() {
        if (!isset($this->login)) {
            $this->login= $this->blBatch->read('login');
        }        
        
        if (!isset($this->password)) {
            $this->password= $this->blBatch->read('password');            
        }
        
        if (!isset($this->email)) {
            $this->email= $this->blBatch->read('adresse mail');
        }        
    }    
    
    public function getId_e() {
        return $this->id_e;
    }

    public function getDenominationEntite() {
        return $this->denominationEntite;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getRole() {
        return $this->role;
    }

    public function setId_e($id_e) {
        $this->id_e = $id_e;
    }

    public function setDenominationEntite($denominationEntite) {
        $this->denominationEntite = $denominationEntite;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    public function setRole($role) {
        $this->role = $role;
    }


    
    
}