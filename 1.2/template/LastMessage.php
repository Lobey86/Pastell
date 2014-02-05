<?php 
//TODO a nettoyer
global $lastMessage;
global $lastError;
if ($lastMessage->getLastMessage()) : ?>
<div class="box_confirm">
	<p>
		<?php echo $lastMessage->getLastMessage()?>
	</p>
</div>
<?php endif;?>

<?php if ($lastError->getLastError()) : ?>
<div class="box_error">
	<p>
		<?php echo $lastError->getLastError()?>
	</p>
</div>
<?php endif;?>
