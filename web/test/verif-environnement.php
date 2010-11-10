<?php

require_once(dirname(__FILE__)."/../init.php");


$extensionNeeded = array("curl","mysql","openssl");

$valeurMinimum = array(
			"PHP" => "5.2",
			"OpenSSL" => '1.0.0a',
			
);


$page_title = "Vérification de l'environnement";


if ( ! ENABLE_VERIF_ENVIRONNEMENT ){
	$lastError->setLastError("La vérification de l'environnement est désactivé sur ce serveur");
	header("Location: index.php");
	exit;
}

$cmd =  OPENSSL_PATH . " version";
$openssl_version = `$cmd`;
$valeurReel['OpenSSL'] = $openssl_version;

$valeurReel['PHP'] = phpversion();

include( PASTELL_PATH ."/include/haut.php");

?>


<div class="box_contenu clearfix">


<h2>Extensions PHP</h2>

<table class='tab_04'>
	<?php foreach($extensionNeeded as $extension) : ?>
		<tr>
			<th><?php echo $extension ?></th>
			<td><?php echo extension_loaded($extension)?"ok":"<b>CETTE EXTENSION N'EST PAS INSTALLE</b>"; ?></td>
		</tr>
	<?php endforeach;?>
</table>

<h2>Valeur minimum</h2>

<table class='tab_04'>
	<tr>
		<th>Element</th>
		<th>Attendu</th>
		<th>Trouvé</th>
	</tr>
	<?php foreach($valeurMinimum as $name => $value) : ?>
	<tr>
		<th><?php echo $name?></th>
		<td><?php echo $value ?></td>
		<td><?php echo $valeurReel[$name] ?></td>
	</tr>
	<?php endforeach;?>
</table>

<h2>Constante</h2>
<table class='tab_04'>
	<tr>
		<th>Element</th>
		<th>Valeur</th>
	</tr>
	<tr>
		<th>OPENSSL_PATH</th>
		<td><?php echo OPENSSL_PATH ?></td>
	</tr>
	<tr>
		<th>Révision</th>
		<td><?php echo "$Revision$" ?></td>
	</tr>
</table>


</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
