<?php 


$gradeSQL = new GradeSQL($sqlQuery);


$url = "document/external-data.php?id_d=$id_d&id_e=$id_e&page=$page&field=$field";

$filiere = $recuperateur->get('filiere');
$cadre_emploi = $recuperateur->get('cadre_emploi');

if ($cadre_emploi){
	$url_prev = $url."&filiere=$filiere";
	$url = "document/external-data-controler.php?id_d=$id_d&id_e=$id_e&page=$page&field=$field&filiere=$filiere&cadre_emploi=". htmlentities($cadre_emploi,ENT_QUOTES,"UTF-8");	
	$tabInfo = $gradeSQL->getLibelle($filiere,$cadre_emploi);
	$next_info = "libelle";
	$info_to_choice = "Choix du grade";
	$prev = "Revenir au choix du cadre d'emploi";
	$bc = "> $filiere > $cadre_emploi > ";
	
} elseif($filiere){
	$url_prev = $url;
	$url .="&filiere=$filiere";
	$tabInfo = $gradeSQL->getCadreEmploi($filiere);
	$next_info = "cadre_emploi";
	$info_to_choice = "Choix du cadre d'emplois";
	$prev = "Revenir au choix de la filière";
	$bc = "> $filiere > ";
	
} else {
	$url_prev = "";
	$bc = "";
	$tabInfo = $gradeSQL->getFiliere();
	$next_info = "filiere";
	$info_to_choice = "Choix de la filière";
}


$page_title = "Grade";


include( PASTELL_PATH ."/include/haut.php");

?>
<a href='document/edition.php?id_d=<?php echo $id_d ?>&id_e=<?php echo $id_e?>&page=<?php echo $page ?>'>« Revenir à l'édition du document <em><?php echo $titre?></em></a>
<br/><br/>


<div class="box_contenu clearfix">
<h2><?php echo $bc ." " .  $info_to_choice ?></h2>
<?php if ($url_prev) : ?>
	<a href='<?php echo $url_prev?>'>« <?php echo $prev ?></a><br/><br/>
<?php endif;?>

<table>
	<?php foreach ($tabInfo as $i => $info) : ?>
		<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
			<td class="w30">		
			<a href='<?php echo $url ?>&<?php echo $next_info ?>=<?php hecho($info['name']) ?>'><?php echo $info['name']?></a>		
		</tr>
	     
	<?php endforeach;?>
</table>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
