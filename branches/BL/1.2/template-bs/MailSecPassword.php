

<div class="box">
	Ce message est protégé par un mot de passe :
	<form action='password-controler.php' method='post'>
		<input type='hidden' name='key' value='<?php hecho($the_key) ?>' />
		<input type='password' name='password' />
		<input type='submit' class='btn' />
	</form>	

</div>
