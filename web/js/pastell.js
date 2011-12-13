
$(document).ready(function() {
	$(".noautocomplete").attr('autocomplete','off');
	
	/*$('input:submit').click(function(){
		$('p').text("Form submiting.....").addClass('submit');
		$('input:submit').attr("disabled", true);	
	});*/
	
	$(".send_button").click(function(){
		$(this.form).append("<input type='hidden' name='" + this.name + "' value='true' />");
		$(this.form).append("<p>Sauvegarde en cours ...</p>");
		$(".send_button").attr('disabled', true);
	})
	
	
});

