/*
* jQuery btcdisplay javascript file
*/

jQuery(document).ready(function(){
	setInterval(function(){
		jQuery("span.btc_price").each(function(){		
		
			var field = jQuery(this);
			var operator = jQuery(this).data('operator');
			var value = jQuery(this).data('value');
			
			var data = {
				'action': 'update_price',
				'operator': operator,
				'value': value,
			};
			
			jQuery.post(btcdisplay.ajax_url, data, function(response) {
				field.html(response);
			});
		})
	}, 30000);
})