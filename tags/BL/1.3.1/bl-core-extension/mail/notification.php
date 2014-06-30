Bonjour, 

Votre dossier <?php echo $info['docObjet']?> vient d'être <?php echo $info['etat']?>.
<?php 
if (isset($info['parapheur_annotation_rejet'])) {
    echo "Motif du rejet : \"" . $info['parapheur_annotation_rejet'] . "\"";
}
?>


Cordialement.

-- 
Ce mail vous est envoyé automatiquement par l'application "Bus Berger-Levrault".
