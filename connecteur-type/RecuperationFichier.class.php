<?php
abstract class RecuperationFichier extends Connecteur {
	
	/**
	 * Liste les fichiers disponible sur le connecteur
	 * @return array
	 */
	abstract public function listFile();
	
	/**
	 * Récupère le fichier sur le connecteur et le sauvegarde sur le système de fichier local
	 * @param string $filename nom du fichier à récuperer (retourné dans la liste de listFile())
	 * @param string $destination_directory emplacement pour sauvegarder le fichier (sans le nom du fichier)
	 * @return boolean true si le fichier a été récupéré et sauvegardé
	 * @throws Exception problème lors de la récuperation
	 */
	abstract public function retrieveFile($filename,$destination_directory);
	
	
	/**
	 * Détruit le fichier sur le connnecteur
	 * @param string $filename nom du fichier à détruire (retourné dans la liste de listFile());
	 */
	abstract public function deleteFile($filename);
		
}