
<div class='box'>

<h2>L'expéditeur a demandé un accusé de réception</h2>
 
<form action='document/action.php' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d ?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='0' />
		
	<input type='hidden' name='action' value='<?php echo $action ?>' />
		
	<input type='submit' class='btn' value='Envoyer un accusé de réception'/>
</form>
</div>