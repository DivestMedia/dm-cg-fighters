(function( $ ){
    'use strict';
    $(function() {
    	var se_ajax_url = ajax_auth_object.ajaxurl;

		$('#ed_fighter,#ed_opponent').suggest(se_ajax_url + '?action=fighter_lookup',{delay:500});
        $('#ed_fighter,#ed_opponent').change(function(){
        	var r_val = $(this).val();
        	if(r_val.length){
        		r_val = r_val.split('|');
        		if(typeof r_val != 'undefined'){
        			$(this).val(r_val[0]);
        			$(this).siblings('input[type="hidden"]').val(r_val[1]);
        		}
        	}
        });
        $('#ed_date').datepicker();
	});
}(jQuery));