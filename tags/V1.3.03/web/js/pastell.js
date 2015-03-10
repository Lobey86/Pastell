
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

function split( val ) {
	return val.split( /,\s*/ );
}

function extractLast( term ) {
	return split( term ).pop();
}

$.fn.pastellAutocomplete = function(autocomplete_url,id_e,mail_only) {
	this.autocomplete({
		source: function( request, response ) {
			$.getJSON( autocomplete_url, {
				term: extractLast( request.term ), "id_e": id_e, "mail-only": mail_only
			}, response ); 
		},
		select: function( event, ui ) {
			var terms = split( this.value );
			terms.pop();
			terms.push( ui.item.value );
			terms.push( "" );
			this.value = terms.join( ", " );
			return false;
		} ,
	});
	return this;
}

