<html>
<head>
<script type="text/javascript" src="../web/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src='../web/js/jquery-ui-1.11.2.min.js'></script> 
     
<script type="text/javascript" src="../web/js/pastell.js"></script>

<link rel="stylesheet" href="../web/img/jquery-ui.theme.1.11.2.min.css">
</head>
<body>
<input id="to" autocomplete='off' name="to" value="" size="40" type="text">
<script>

$(document).ready(function(){
	$("#to").pastellAutocomplete("../web/mailsec/get-contact-ajax.php");
});
</script>
</body>
</html>