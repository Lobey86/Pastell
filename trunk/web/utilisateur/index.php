<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");

$recuperateur = new Recuperateur($_GET);
$offset = $recuperateur->getInt('offset',0);
$search = $recuperateur->get('search');

$droit_lecture = $roleUtilisateur->hasOneDroit($authentification->getId(),"utilisateur:lecture");

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}

$utilisateurListe = new UtilisateurListe($sqlQuery);

$utilisateurListeHTML = new UtilisateurListeHTML();
if ($roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:edition",0)){
	$utilisateurListeHTML->addDroitEdition();
}
	
$nbUtilisateur = $utilisateurListe->getNbUtilisateur($search);

$page_title = "Liste des utilisateurs";
include( PASTELL_PATH ."/include/haut.php");
?>
<div>
<form action='utilisateur/index.php' method='get' >
	<input type='text' name='search' value='<?php echo $search?>'/>
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php 


suivant_precedent($offset,50,$nbUtilisateur,"utilisateur/index.php?search=$search");

$utilisateurListeHTML->displayAll($utilisateurListe->getAll($offset,50,$search));

include( PASTELL_PATH ."/include/bas.php");

