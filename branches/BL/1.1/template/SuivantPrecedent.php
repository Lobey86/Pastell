<div class="box_suiv">
	<div class="prec">
		<?php if ( $offset) : ?>
			<a href="<?php echo $link ?>offset=<?php echo max(0,$offset-$limit); ?>"><?php echo _("Précédent") ?></a>
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