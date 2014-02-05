
<div>
<p>Vous devez cliquez sur le lien du mail qui a été envoyé à :
 <b><?php echo $infoUtilisateur['email']; ?></b></p>

<p>
Vous pouvez également saisir le code qui vous a été envoyé dans le mail : 
</p>
<form action='inscription/fournisseur/mail-validation-controler.php' method='get' >
	<input type='text' name='chaine_verif' value='' />
</form>

<br/>

<p>Si ce n'est pas la bonne adresse email, vous pouvez <a href='inscription/fournisseur/desincription.php'>recommencer la procédure</a> </p>

<p>Nous pouvons également <a href='inscription/fournisseur/renvoie-mail-inscription.php'>renvoyer le mail</a></p>
</div>
