<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");


$page_title="Gestion des documents";

include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">

<h2>Gestion des documents</h2>


	<a href='document/edition.php?form_type=rh-actes'>Nouveau Actes RH</a>

</div>


<?php 
include( PASTELL_PATH ."/include/bas.php");
