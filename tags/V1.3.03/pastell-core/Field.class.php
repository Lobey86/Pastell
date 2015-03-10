<?php

/**
 * La classe Field représente un champ d'un formulaire Pastell défini dans un fichier de type definition.yml, entite-properties.yml ou global-properties.yml
 */
class Field {
	
	
	const LIBELLE_PROPERTIES_KEY = 'name'; /** Clé permettant de définir le libellé (lisible par un humain). Cette clé est improprement appelée "name" !*/
	const INDEX_PROPERTIES_KEY = 'index'; /** Clé permettant d'indiquer si le champs doit-être indexé. La valeur de la clé est true ou false */
	const VISIONNEUSE_PROPERTIES_KEY = 'visionneuse'; /** Clé permettant d'indiquer le nom d'une classe utilisé pour visualisé le fichier */
	
	private $fieldName;
	private $properties;
	
	/**
	 * Le nom des champs ne contient que des chiffres,lettres en minuscule et le caractère _. 
	 * Les autres charactères sont remplacés par un souligné, les lettres avec diacritique (accent, cédille) sont remplacé par leur variante sans diacritique.
	 * 
	 * CELA PROVIENT DES PREMIERES VERSIONS DE PASTELL, IL N'EST PAS CONSEILLE D'UTILISER DES NOMS DE CLES AVEC AUTRES CHOSES QUE DES LETTRES MINUSCULES, CHIFFRES
	 * ET SOULIGNE
	 * 
	 * @deprecated
	 * @param string $field_name
	 * @return string
	 */
	public static function Canonicalize($field_name){	
		$name = strtolower($field_name);
		$name = strtr($name," àáâãäçèéêëìíîïñòóôõöùúûüýÿ","_aaaaaceeeeiiiinooooouuuuyy");
		$name = preg_replace('/[^\w_]/',"",$name);
		return $name;
	}
	
	/**
	 * @param string $fieldName nom du champs
	 * @param array $properties propriétés associés au champs
	 */
	public function __construct($fieldName,$properties){
		$this->fieldName = $fieldName;
		$this->properties = $properties;
	}
	
	/**
	 * 
	 * @return string retourne le nom de ce champ;
	 */
	public function getName(){
		return self::Canonicalize($this->fieldName);
	}
	
	/**
	 * @return string Le libellé a affiché à l'utilisateur (human-readable). Si la clé du libellé n'est pas défini, on renvoie le nom du champs (non-canonicalisé);
	 */
	public function getLibelle(){
		if (isset($this->properties[self::LIBELLE_PROPERTIES_KEY])){
			return $this->properties[self::LIBELLE_PROPERTIES_KEY];
		}
		return $this->fieldName;
	}
	
	public function isRequired(){
		return  (! empty($this->properties['requis']));
	}
	
	public function getType(){
		if (!empty($this->properties['type'])){
			return $this->properties['type'];
		}
		return "text";
	}
	
	public function isMultiple(){
		return  (! empty($this->properties['multiple']));
	}
	
	public function getSelect(){
		return $this->properties['value'];
	}
	
	public function getDefault(){
		if ($this->getType() == 'date' && ! $this->getProperties('default') ){
			return date("Y-m-d");
		}
		return $this->getProperties('default');
	}
	
	public function isTitle(){
		return (! empty($this->properties['title']));
	}
	
	public function getOnChange(){
		return $this->getProperties('onchange');
	}
	
	public function pregMatch(){
		return $this->getProperties('preg_match');
	}
	
	public function pregMatchError(){
		return $this->getProperties('preg_match_error');
	}
	
	public function getProperties($properties){
		if ( ! isset($this->properties[$properties])){
			return false;
		}
		return $this->properties[$properties];
	}
	
	public function getAllProperties(){
		$result = $this->properties;
		if (empty($result['name'])){
			$result['name'] = $this->getLibelle(); 
		}
		return $result;
	}
	
	public function isEnabled($id_e,$id_d){
		
		$action_name = $this->getProperties('choice-action');
		if ( ! $action_name){
			return true;
		}
		
		global $objectInstancier;
		$id_u = $objectInstancier->Authentification->getId();
		try { 
			return $objectInstancier->ActionExecutorFactory->isChoiceEnabled($id_e,$id_u,$id_d,$action_name);
		} catch (Exception $e){
			return false;
		}
	}
	
	public function isShowForRole($role){
		if ($this->getProperties('no-show')){
			return false;
		}
	
		$show_role = $this->getProperties('show-role') ;
	
		if (! $show_role){
			return true;
		}
	
		foreach($show_role as $role_unit){
			if ($role == $role_unit){
				return true;
			}
		}
		return false;
	}
	
	public function isIndexed(){
		return $this->getProperties(self::INDEX_PROPERTIES_KEY);
	}
	
	public function getVisionneuse(){
		return $this->getProperties(self::VISIONNEUSE_PROPERTIES_KEY);
	}
	
}