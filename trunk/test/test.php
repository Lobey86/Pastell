<html>
<head>
<script type="text/javascript" src="../web/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src='../web/js/jquery-ui.min.js'></script> 
     
<script type="text/javascript" src="../web/js/htmlentities.js"></script>
</head>
<body>
<input id="to" autocomplete='off' name="to" value="" size="40" type="text">
<script>



$(document).ready(function(){
	$("#to").autocomplete({
		 source: function( request, response ) {
             $.ajax({
                 url:  "../web/mailsec/get-contact-ajax.php",
                 data: {q: request.term,id_e: 1},
                 success: function(data) {
                             /*response($.map(data, function(item) {
                             return {
                                 label: item.state,
                                 id: item.id,
                                 abbrev: item.abbrev
                                 };
                         }));*/
                         var result = data.split("\n");
                         console.log(result);
                         response(result);
                         
                     
                     }
                 })},
		multiple: true,
		cacheLength:0,
		max: 20

	
	
	});
});
</script>
</body>
</html>