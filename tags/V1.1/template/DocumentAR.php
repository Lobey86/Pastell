
<div>

<h2>L'expediteur a demandé un accusé de réception</h2>
 
<form action='document/action.php' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d ?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='0' />
		
	<input type='hidden' name='action' value='<?php echo $action ?>' />
		
	<input type='submit' value='Envoyer un accusé de réception'/>
</form>
</div>