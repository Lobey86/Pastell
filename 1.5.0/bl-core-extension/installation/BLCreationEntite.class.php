<?php

class BLCreationEntite {
    
    private $denomination;
    private $siren;
    
    private $blBatch;
    
    public function __construct($blBatch) {
        $this->blBatch = $blBatch;
    }
    
    public function creerEntite() {
        $this->demanderSaisieInformationEntite();
        if ($this->controlerInformationEntite()) {
            global $objectInstancier;
            $id_e = $objectInstancier->EntiteControler->edition(null, $this->denomination, $this->siren, 'collectivite', 0, 0, 'non', 'non');            
            return $id_e;
        } else {
            throw new Exception ("Les informations saisies sont incorrectes.");
        }        
    }
    
    private function controlerInformationEntite() {
        return true;
    }
        
    private function demanderSaisieInformationEntite() {
        if (!isset($this->denomination)) {
            $this->denomination = $this->blBatch->read('denomination');
        }        
        if (!isset($this->siren)) {
            $this->siren = $this->blBatch->read('siren valide');            
        }       
    }
    
    public function getDenomination() {
        return $this->denomination;
    }

    private function getSiren() {
        return $this->siren;
    }

    public function setDenomination($denomination) {
        $this->denomination = $denomination;
    }

    public function setSiren($siren) {
        $this->siren = $siren;
    }    
}