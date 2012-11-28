<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');

$droit_lecture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e);

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
if ($id_e && ! $entite->exists()){
	header("Location: index.php");
	exit;
}
$info = $entite->getInfo();



if ($id_e){
	$page_title = "Configuration des connecteurs pour « {$info['denomination']} »";
} else {
	$page_title = "Configuration des connecteurs globaux";
}

if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",0)){
	$nouveau_bouton_url = array("Nouveau" => "connecteur/new.php?id_e=$id_e");
}

$i = 0;

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<?php if($id_e): ?>
<a href='entite/detail.php?id_e=<?php echo $id_e?>'>« Revenir à <?php echo $info['denomination']?></a>
<?php else: ?>
<a href='entite/index.php'>« Revenir à la liste des collectivités</a>
<?php endif;?>

<br/><br/>
<?php 
include( PASTELL_PATH ."/include/bas.php");
