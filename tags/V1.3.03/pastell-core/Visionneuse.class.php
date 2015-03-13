<?php
abstract class Visionneuse {
	
	/**
	 * Affiche en le transformant le contenu d'un fichier
	 * @param string $filename nom original du fichier à afficher
	 * @param string $filepath emplacement du fichier dans le workspace
	 */
	abstract public function display($filename,$filepath);	
	
}