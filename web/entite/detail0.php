<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteDetailHTML.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentListHTML.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/helper/date.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");


$recuperateur = new Recuperateur($_GET);
$tab_number = $recuperateur->getInt('page',0);
$offset = $recuperateur->getInt('offset',0);
$droit = $recuperateur->get('droit','');
$descendance = $recuperateur->get('descendance','');

$id_e=0;
$entite = new Entite($sqlQuery,$id_e);

$droit_lecture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e);

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}


$page_title = "Propriétés globales";

$formulaire_tab = array("Utilisateurs","Annuaire globale");

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>


<a href='entite/index.php'>« liste des collectivités</a>
<br/><br/>
	
	<div id="bloc_onglet">
		<?php foreach ($formulaire_tab as $page_num => $name) : ?>
					<a href='entite/detail0.php?id_e=<?php echo $id_e ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_number)?'class="onglet_on"':'' ?>>
					<?php echo $name?>
					</a>
		<?php endforeach;?>
		</div>
	<div class="box_contenu clearfix">
	
<?php 
if ($tab_number == 0){

	$utilisateurListe = new UtilisateurListe($sqlQuery);
	$utilisateurListeHTML = new UtilisateurListeHTML();
	$utilisateurListeHTML->addDroit($allDroit);
	
	if ($roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:edition",$id_e)){
		$utilisateurListeHTML->addDroitEdition();
	}
	if ($descendance){
		$all_id_e = $entite->getDescendance($id_e);
	} else {
		$all_id_e = array($id_e);
	}
	
	if ($droit){
		$allUtilisateur = $utilisateurListe->getUtilisateurByEntiteAndDroit($all_id_e,$droit);
	} else {
		$allUtilisateur = $utilisateurListe->getUtilisateurByEntite($all_id_e);
	}
	
	$utilisateurListeHTML->display($allUtilisateur,$id_e,$droit,$descendance,"entite/detail0.php",0);
	} elseif ($tab_number == 1) { ?>
	<a href='mailsec/annuaire.php'>Annuaire global »</a>
<?php 

	
} ?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
