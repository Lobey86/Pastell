<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<base href='http://192.168.1.5/adullact/pastell/web/' />
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>

	</head>
	<body>
	<form action='index.php' method='post' enctype="multipart/form-data" id='myform'>
	
			<input type='submit' name='enregistrer' value='Enregistrer' class='send_button' />
	
	</form>
	<script>

	$(document).ready(function() {
		
		$(".send_button").click(function(){
			$(this.form).append("<input type='hidden' name='" + this.name + "' value='true' />");
			$(this.form).append("<p>Sauvegarde en cours ...</p>");
			$(".send_button").attr('disabled', true);
			//$("#myform").submit();
			$(this.form).submit();
			
		})
		
	});

		
	</script>
	</body>
	
	
</html>

<?php
