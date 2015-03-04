Bonjour, 

Le système Pastell vous envoie les notifications suivantes (résumé journalier) : 


<?php foreach($info as $info_notification):?>

*******************
<?php echo $info_notification['message']?>

URL de consultation du document : 
<?php echo SITE_BASE ?>/document/detail.php?id_d=<?php echo $info_notification['id_d']."&" ?>id_e=<?php echo $info_notification['id_e']?>

*******************

<?php endforeach;?>


-- 
Pastell - <?php echo SITE_BASE ?>

Vous recevez ce message car vous avez sélectionner la réception d'un résumé journalier sur certain type de message .

