<?php

class TransactionEtatBouton {
	
	private $id_t;
	private $action;
	
	public function __construct($id_t,$action){
		$this->id_t = $id_t;
		$this->action = $action;
	}
	
	function affiche($etat,$texte){ ?>
	<form action='<?php echo $this->action ?>' method='post'>
		<input type='hidden' name='id_t' value='<?php echo $this->id_t ?>'/>
		<input type='hidden' name='etat' value='<?php echo $etat?>'/>
		<input type='submit' value="<?php echo $texte?>" />
	</form>
	<?php }
	
}