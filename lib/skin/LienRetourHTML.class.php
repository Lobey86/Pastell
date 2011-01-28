<?php


class LienRetourHTML {
	
	public function display($name,$link){ ?>
		<div class='lien_retour'>
			<a href='<?php echo $link ?>'>« <?php echo $name ?></a>
		</div>
	<?php 
	}
	
}