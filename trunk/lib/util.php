<?php

function hecho($message,$quot_style=ENT_QUOTES){
	echo htmlentities($message,$quot_style);
}

function dateInput($name,$value=''){
	?>
	<input 	type='text' 	
								id='<?php echo $name?>' 
								name='<?php echo $name?>' 
								value='<?php echo $value?>' 
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

function getDateIso($value){
	if ( ! $value){
		return "";
	}
	return preg_replace("#^(\d{2})/(\d{2})/(\d{4})$#",'$3-$2-$1',$value);
}


function rrmdir($dir) {	
	if (! is_dir($dir)) {
		return;
	}
	foreach ( scandir($dir) as $object) {
		if (in_array($object,array(".",".."))) {
			continue;
		}
		if (is_dir("$dir/$object")){
			rrmdir("$dir/$object");
		} else {
			unlink("$dir/$object");
		}
	}
	rmdir($dir);
}


function get_argv($num_arg) {
	global $argv;
	if (empty($argv[$num_arg])){
		return false;
	}
	return $argv[$num_arg];
};
