<div class='lien_retour'>
	<a href='utilisateur/moi.php'>« Espace utilisateur</a>
</div>
<div class="box_contenu clearfix">

<?php if($result) : ?>
<div class="box_confirm">

	<p>Votre email a été validé.</p>
	
	
	<p>Votre administrateur doit maintenant valider votre changement d'email</p>
	<p>Vous serez averti par email </p>
</div>	
<?php else : ?>
<div class="box_error">

<p>Un problème empêche de satisfaire votre demande</p>
<p>Veuillez recommencer la procédure de changement d'email.</p>
</div>	
<?php endif;?>

</div>
