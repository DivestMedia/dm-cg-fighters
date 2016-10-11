(function( $ ){
    'use strict';
    $(function() {

    	$('#_fr_winner').change(function(){
    		var win = $(this).val();
    		$('.round-record-container').find('.cont-res').empty().hide();
    		$('.round-record-container').find('.'+win.toLowerCase().replace(/ /g,"_")).each(function(){
    			$(this).find('.cont-res').html('Winner').show();
    		});
    	});

    	$('#_fr_result').change(function(){
    		if($(this).val()=='draw'){
    			$('.cont_fr_winner').hide();
    			$('#_fr_winner').val('-1');
    			$('.round-record-container').find('.cont-res').empty().hide();
    		}else{
    			$('.cont_fr_winner').show();
    		}
    	});

    	$('.cont-round').on('change','input',function(){
    		var inp_changed = $(this).attr('class');
    		var new_val = [];
    		$('.'+inp_changed).each(function(){
    			if($(this).val().length)
    			new_val.push($(this).val());
    		});
    		$('#'+inp_changed).val(new_val.join());
    	});

    	$('.round-container').on('click','div',function(){
    		var tab = $(this).text();
    		$(this).addClass('active').siblings().removeClass('active');
    		$('.round-record-container').find('.round-'+tab).addClass('active').siblings('.cont-round').removeClass('active');
    	});
    	$('.cont-record-label').on('click','label',function(){
    		var tab = $(this).data('tab');
    		$(this).addClass('active').siblings().removeClass('active');
    		$(this).parents('.round-record-container').find('.tab-'+tab).addClass('active').siblings('.cont-record-content').removeClass('active');
    	});

    	$('#fight-nav-container').on('click','.fight-nav',function(){
    		var tab = $(this).data('tab');
    		$(this).addClass('active').siblings().removeClass('active');
    		$('#fight-main-container').find('#'+tab).fadeIn(500).siblings().fadeOut(0);
    	});
    	$('.dd-fighter-category').change(function(){
			var cur = $(this);
			var fighter_dd = cur.siblings('.dd-fighters');
			var term_id = cur.val();
			var selected_fighter = fighter_dd.data('selected');
			$('#_fr_winner').html('<option value="-1">-- Winner --</option>');
			fighter_dd.append('<option disabled selected>-- Getting all the fighters --</option>')
			$.ajax({
				type: 'POST',
				url: ajax_auth_object.ajaxurl,
				data: {
					action: 'ajaxgetfightersbycategory',
					cat: term_id,
				},
				success: function(data){
					data = JSON.parse(data);
					if(data.length){
						fighter_dd.empty();
						var isselected = '';
						var winner = $('#_fr_winner').data('value');
						$.each(data,function(i,f){
							isselected = selected_fighter==f.id?'selected="selected"':'';
							fighter_dd.append('<option value="'+f.id+'" '+isselected+'>'+f.name+'</option>');
							fighter_dd.find('option:last-child').data('details',f);
							if(isselected.length){
								isselected = winner==f.name?'selected="selected"':'';
								$('#_fr_winner').append('<option '+isselected+'>'+f.name+'</option>');
							}

						});
					}else{
						fighter_dd.html('<option disabled selected>-- No fighters available --</option>');
					}
					setTimeout(function(){$('.dd-fighters').trigger('change');},1000);
				},
				error: function(errorThrown){
				  	console.log(errorThrown);
				} 
			});
		});	
		$('.dd-fighter-category').trigger('change');

		$('.dd-fighters').change(function(){
			var cur = $(this);
			var pos = cur.data('pos');
			var id = cur.val();
			var od = cur.find('option:selected').data('details');
			var fn = cur.find('option:selected').text();
			if(typeof od != 'undefined'){
				cur.siblings('.cont-image').html('<img src="'+od['image'+pos]+'">').show();
				cur.siblings('.record').html(od.win+' - '+od.loss+' - '+od.draw);
				cur.siblings('.nickname').html(od.nickname.length?od.nickname:'NA');
				cur.siblings('.age').html(od.age);
				cur.siblings('.height').html(od.height);
				cur.siblings('.weight').html(od.weight);
				cur.siblings('.reach').html(od.reach);
				cur.siblings('.legreach').html(od.legreach);
				cur.siblings('.striking').html(od.striking);
				cur.siblings('.striking_attempts').html(od.striking_attempts);
				cur.siblings('.striking_defense').html(od.striking_defense);
				cur.siblings('.takedown').html(od.takedown);
				cur.siblings('.takedown_attempts').html(od.takedown_attempts);
				cur.siblings('.takedown_defense').html(od.takedown_defense);
				$('.fimage-cont-'+pos).html('<img src="'+od.fimage+'">');
				$('.fighter-'+pos+'-name').html(fn+'<span class="cont-res"></span>').addClass(fn.toLowerCase().replace(/ /g,"_"));
			}else{
				cur.siblings('.cont-image').hide();
				cur.siblings('.record').html('NA');
				cur.siblings('.nickname').html('NA');
				cur.siblings('.age').html('NA');
				cur.siblings('.height').html('NA');
				cur.siblings('.weight').html('NA');
				cur.siblings('.reach').html('NA');
				cur.siblings('.legreach').html('NA');
				cur.siblings('.striking').html('NA');
				cur.siblings('.striking_attempts').html('NA');
				cur.siblings('.striking_defense').html('NA');
				cur.siblings('.takedown').html('NA');
				cur.siblings('.takedown_attempts').html('NA');
				cur.siblings('.takedown_defense').html('NA');
				$('.fighter-'+pos+'-name').text('NA');

			}
			$('#_fr_winner').trigger('change');
		});
	});
}(jQuery));