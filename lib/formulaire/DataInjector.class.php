<?php


class DataInjector{
	
	public function __construct(Formulaire $formulaire, DonneesFormulaire $donneFormulaire){
		$this->formulaire = $formulaire;
		$this->donneFormulaire = $donneFormulaire;
	}
	
	public function inject($siren){
		foreach( $this->formulaire->getAllFields() as $field){
			if ($field->getProperties("read-only")) {
				$this->donneFormulaire->injectData($field->getName(),$siren);
			}
		}
	}
	
}