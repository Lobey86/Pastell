<?php 
require_once( PASTELL_PATH . "/lib/Siren.class.php");

$siren = new Siren();
?>
<div class="box_info">
<p><strong>Version de démonstration</strong></p>
<p>Exemple de siren valide : <?php echo $siren->generate()?> </p>

</div>