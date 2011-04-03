<?php
require_once("init-api.php");

$liste_collectivite = $roleUtilisateur->getAllEntiteWithFille($id_u,'entite:lecture');
$JSONoutput->display($liste_collectivite);
