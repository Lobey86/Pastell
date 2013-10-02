<?php if (! PRODUCTION) : ?>
<div class="alert alert-info">
<strong>Version de démonstration</strong>
<br/>
Exemple de siren valide : <?php hecho($this->Siren->generate()) ?>
</div>
<?php endif;?>