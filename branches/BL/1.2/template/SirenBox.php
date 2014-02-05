<?php if (! PRODUCTION) : ?>
<div class="box_info">
<p><strong>Version de démonstration</strong></p>
<p>Exemple de siren valide : <?php hecho($this->Siren->generate()) ?> </p>

</div>
<?php endif;?>