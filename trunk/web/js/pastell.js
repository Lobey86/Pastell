
$(document).ready(function() {
	$(".noautocomplete").attr('autocomplete','off');
	
	$(".send_button").click(function(){
		$(this.form).append("<input type='hidden' name='" + this.name + "' value='true' />");
		$(this.form).append("<p>Sauvegarde en cours ...</p>");
		$(".send_button").attr('disabled', true);
		$(this.form).submit();
	})
	
	$('#select-all').click(function(event) {
		var result = this.checked;
		$(':checkbox').each(function() {
	            this.checked = result;                        
		});
	});
	
});

