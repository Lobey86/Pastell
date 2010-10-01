<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');


$recuperateur = new Recuperateur($_GET);
$id_d = $recuperateur->get('id_d');
$type = $recuperateur->get('type');
$id_e = $recuperateur->get('id_e');


$document = new Document($sqlQuery);

if ($id_d){
	$info = $document->getInfo($id_d);
	$type = $info['type'];
} else {
	$id_d = $document->getNewId();	
}

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$formulaire_file = PASTELL_PATH . "/form/$type.yml" ;
if ( ! file_exists($formulaire_file)){
	header("Location: index.php");
	exit;
}

$formulaire = new Formulaire( $formulaire_file);

$tab = $formulaire->getTab();
$page_title="Edition d'un document « " . $tab[0] . " » ( " . $infoEntite['denomination'] . " ) ";


$donneesFormulaire = new DonneesFormulaire( WORKSPACE_PATH . "/$id_d.yml");
$donneesFormulaire->setFormulaire($formulaire);

$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->injectHiddenField("id_d",$id_d);
$afficheurFormulaire->injectHiddenField("form_type",$type);
$afficheurFormulaire->injectHiddenField("id_e",$id_e);

include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">
<?php $afficheurFormulaire->affiche(0,"document/edition-controler.php","document/recuperation-fichier.php?id_d=$id_d"); ?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
