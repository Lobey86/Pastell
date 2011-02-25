<?php

function hecho($message,$quot_style=ENT_QUOTES){
	echo htmlentities($message,$quot_style);
}

function dateInput($name){
	?>
	<input 	type='text' 	
								id='<?php echo $name?>' 
								name='<?php echo $name?>' 
								value='' 
								class='date'
								/>
							<script type="text/javascript">
						   		 jQuery.datepicker.setDefaults(jQuery.datepicker.regional['fr']);
								$(function() {
									$("#<?php echo $name?>").datepicker( { dateFormat: 'dd/mm/yy' });
									
								});
							</script>
	<?php 
}