<?php 
//TODO a nettoyer
global $lastMessage;
global $lastError;
if ($lastMessage->getLastMessage()) : ?>
<div class="alert alert-success">
	<?php echo $lastMessage->getLastMessage()?>
</div>
<?php endif;?>

<?php if ($lastError->getLastError()) : ?>
<div class="alert alert-error">
	<?php echo $lastError->getLastError()?>
</div>
<?php endif;?>
