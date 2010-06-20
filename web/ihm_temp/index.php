<?php
$page = "accueil";
$page_title = "Titre H1 ici";
include ("incHaut.php");
?>

<h2>Tableau 1</h2>


<div class="box_suiv">
	<div class="prec">Précédent</div>
	 <div class="milieu">Position 1 à 12 sur 456</div>
	 <div class="suiv">Suivant</div>
</div>


<table class="tab_01">
<tr>
<th>colonne 1</th>
<th>colonne 2</th>
<th>colonne 3</th>
<th>colonne 4</th>
</tr>
<?php
$bg_class = "bg_class_gris";
for ($i=1 ; $i<6 ; $i++) :
	if ( $bg_class == "bg_class_blanc" ) $bg_class = "bg_class_gris";
	else $bg_class = "bg_class_blanc";
	?>
	<tr class="<?php echo $bg_class ?>">
	<td>cellule 1</td>
	<td>cellule 1</td>
	<td>cellule 1</td>
	<td>cellule 1</td>
	</tr>
<?php
endfor;
?>
</table>

<h2>Tableau 2</h2>
<table class="tab_02">
<tr>
<th>colonne 1</th>
<th>colonne 2</th>
<th>colonne 3</th>
<th>colonne 4</th>
</tr>
<?php
$bg_class = "bg_class_vert";
for ($i=1 ; $i<4 ; $i++) :
	if ( $bg_class == "bg_class_blanc" ) $bg_class = "bg_class_vert";
	else $bg_class = "bg_class_blanc";
	?>
	<tr class="<?php echo $bg_class ?>">
	<td>cellule 1</td>
	<td>cellule 1</td>
	<td>cellule 1</td>
	<td>cellule 1</td>
	</tr>
<?php
endfor;
?>
</table>

<h2>Tableau 3</h2>
<table class="tab_03">
<tr>
<th>colonne 1</th>
<th>colonne 2</th>
<th>colonne 3</th>
<th>colonne 4</th>
</tr>
<?php
$bg_class = "bg_class_orange";
for ($i=1 ; $i<4 ; $i++) :
	if ( $bg_class == "bg_class_blanc" ) $bg_class = "bg_class_orange";
	else $bg_class = "bg_class_blanc";
	?>
	<tr class="<?php echo $bg_class ?>">
	<td>cellule 1</td>
	<td>cellule 1</td>
	<td>cellule 1</td>
	<td>cellule 1</td>
	</tr>
<?php
endfor;
?>
</table>

<h2>Formulaire</h2>
toto
<?php include ("incBas.php") ?>