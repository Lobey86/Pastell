<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");


require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/document/Document.class.php");



$recuperateur = new Recuperateur($_GET);
$id_d = $recuperateur->get('id_d');

$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);

$donneesFormulaire = new DonneesFormulaire(WORKSPACE_PATH  . "/$id_d.yml");

$formulaire = $donneesFormulaire->getFormulaire();
$formulaire->setTabNumber(0);


$page_title = "Document " .  $id_d;


include( PASTELL_PATH ."/include/haut.php" );


$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->afficheTab(0,"document/voir.php?id_d=$id_d");
?>
<div class="box_contenu">
<?php 
$afficheurFormulaire->afficheStatic(0,"document/recuperation-fichier.php?id_d=$id_d");
?>
<br/>
<form action='document/edition.php' method='get' >
	<input type='hidden' name='id_d' value='<?php echo $id_d?> ' />
	<input type='submit' value='Editer'/>
</form>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php" );
