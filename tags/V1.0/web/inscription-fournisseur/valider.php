<?php
require_once("init-information.php");


if (! $donneesFormulaire->isValidable()) {
	$lastError->setLastError("Le formulaire n'est pas terminé");
	header("Location: index.php");
}

$page_title= "Validation du formulaire";
include( PASTELL_PATH . "/include/haut.php");
?>

<div class="box_contenu clearfix">

<div class="box_alert">
<p>
En cliquant sur le bouton suivant, l'ensemble de vos informations 
sera soumis à la collectivité.
</p>
</div>

<form action='inscription-fournisseur/valider-controler.php'>
	<input type='submit' value='Envoyer mes informations' />
</form>
</div>

<?php 
include( PASTELL_PATH . "/include/bas.php");
