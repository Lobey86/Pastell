<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

require_once (PASTELL_PATH . "/lib/helper/date.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e',0);
$tab_number = $recuperateur->getInt('page',0);

$info = $objectInstancier->EntiteSQL->getInfo($id_e);

if ($id_e){
	$page_title = "Détail " . $info['denomination'];
	$formulaire_tab = array("Informations générales","Utilisateurs","Agents","Connecteurs","Flux" );
} else {
	$formulaire_tab = array("Entité","Utilisateurs","Agents","Connecteurs","Annuaire" );
	$page_title = "Administration";
}

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<?php if ($id_e) : ?>
<a href='entite/detail.php'>« Administration</a>
<?php endif; ?>
<br/><br/>

<div id="bloc_onglet">
	<?php foreach ($formulaire_tab as $page_num => $name) : ?>
		<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_number)?'class="onglet_on"':'' ?>>
			<?php echo $name?>
		</a>
	<?php endforeach;?>
</div>
<div class="box_contenu clearfix">

<?php 
if ($tab_number == 0) :
	if ($id_e){ 
		$objectInstancier->EntiteControler->detailEntite();
	} else {
		$objectInstancier->EntiteControler->listEntite();
	}
elseif($tab_number == 1) : 
	$objectInstancier->EntiteControler->listUtilisateur();
elseif($tab_number == 2) :
	$objectInstancier->AgentControler->listAgent();
	
elseif($tab_number==3) :
	$objectInstancier->ConnecteurControler->listConnecteur();
elseif($tab_number == 4 ) :
	if ($id_e){
		$objectInstancier->FluxControler->listFlux();
	} else {
		?><a href='mailsec/annuaire.php'>Annuaire global »</a><?php 
	}
 endif;
 ?>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
