<?php 
if ($this->LastMessage->getLastMessage()) : ?>
<div class="alert alert-success">
	<?php echo $this->LastMessage->getLastMessage()?>
</div>
<?php endif;?>

<?php if ($this->LastError->getLastError()) : ?>
<div class="alert alert-error">
	<?php echo $this->LastError->getLastError()?>
</div>
<?php endif;?>
