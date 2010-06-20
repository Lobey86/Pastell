<?php 
function suivant_precedent($offset,$limit,$nb_total,$link = null,$message=null) {
	
	if (! $message){
		$message = _('Position %1$s à %2$s sur %3$s');
	}
	
	if (! $link){
		$link = $_SERVER['PHP_SELF'];
	}
	if ( strstr($link,"?")){
		 $link = $link."&";
	} else {
		 $link = $link."?";
	}
?>
<div class="box_suiv">
	<div class="prec">
		<?php if ( $offset) : ?>
			<a href="<?php echo $link ?>offset=<?php echo $offset-$limit; ?>"><?php echo _("Précédent") ?></a>
		<?php else : ?>
			&nbsp;
		<?php endif; ?>
	</div>
	 <div class="milieu"><?php echo sprintf ( $message, ($offset+1), min($offset+$limit,$nb_total),$nb_total ); ?></div>
	 <div class="suiv">
	 	<?php if(($offset+$limit) < $nb_total) : ?>
	 		<a href="<?php echo $link ?>offset=<?php echo $offset+$limit ?>"><?php echo _("Suivant") ?></a>
	 	<?php else : ?>
			&nbsp;
		<?php endif; ?>
	 </div>
</div>
<?php
} 
