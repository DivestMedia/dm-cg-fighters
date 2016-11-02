(function( $ ){
    'use strict';
    var cur = 0;
    $(function() {
        var cur_sel_vid = [];
        $('.fighter-vid-preview-container').on('click','.cont-thumb-img',function(){
            var id = $(this).data('id');
            $(this).toggleClass('selected');
            var sel_vid = [];
            $('.fighter-vid-preview-container').find('.selected').each(function(){
                sel_vid.push($(this).data('id'));
            });
            $('input[name="_uf_video"]').val(sel_vid.join());
        });

        $('.btn-get-all-videos').click(function(){
            var sel_vid_ids = $('input[name="_uf_video"]').val();
            cur_sel_vid = sel_vid_ids.split(',');
            var vidsContainer = $('.fighter-vid-preview-container');
            $.ajax({
                url: "/wp-admin/admin-ajax.php",
                dataType: 'json',
                type: 'POST',
                data: {
                    'action':'getallvideo',
                },
                success: function(result){
                    if(result.length){
                        var videos = '';
                        $.each(result,function(i,v){
                            var issel = '';
                            var selected_icon = '';
                            if(cur_sel_vid.indexOf(v.id.toString())>=0){
                                issel = 'selected';
                                selected_icon = '<span class="dashicons dashicons-yes"></span>';
                            }
                            vidsContainer.append( '<div class="cont-thumb-img '+issel+'" data-id="'+v.id+'" data-url="'+v.thumbnail+'" style="background: url('+v.thumbnail+') no-repeat center center;background-size: cover;">'+selected_icon+'</div>' );
                        });
                    }
                },
                error: function(errorThrown){console.log(errorThrown);}
            });
        });

        if($( ".fighter-img-container" ).find('div').length){
              $( ".fighter-img-container" ).sortable({
                stop: function (evet, ui){
                    var imgid = [];
                    $('.fighter-img-container').find('.cont-thumb-img').each(function(){
                        imgid.push($(this).data('id'));
                    });
                    $('input[name="_uf_image"]').val(imgid.join());
                  // $('#container-admin-menu').find('.btn-delete-slider').each(function(){
                  //   $(this).data('pos',pos++);
                  // });
                }
              });
              $( ".fighter-img-container" ).disableSelection();
        }

      $('.btn-fighter-update-featured').click(function(){
        var cur_btn = $(this);
        $.ajax({
            url: "/wp-admin/admin-ajax.php",
            dataType: 'json',
            type: 'POST',
            data: {
                'action':'updatefeaturedfighter',
                'post_ID': $(this).data('id')
            },
            success: function(result){
                if(result.status==1){
                  cur_btn.find('span').addClass('dashicons-star-filled').removeClass('dashicons-star-empty');
                }else if(result.status==2){
                  cur_btn.find('span').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                }
            },
            error: function(errorThrown){console.log(errorThrown);}
        });
      });
        var frame,
        metaBox = $('#fighter_images'),
        addImgLink = metaBox.find('.upload-custom-img'),
        imgContainer = metaBox.find( '.fighter-img-container'),
        imgIdInput = metaBox.find( '[name="_uf_image"]' );

        addImgLink.on( 'click', function( event ){
            event.preventDefault();
            if ( frame ) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: 'Select or Upload Fighter Images',
                button: {
                    text: 'Add to fighter images'
                },
                multiple: 'add'
            });
            frame.on( 'select', function() {
                var attachment = frame.state().get('selection').toJSON();
                var attachmentids = [];
                imgContainer.empty();
                $.each(attachment,function(i,v){
                    if(typeof v.url != 'undefined'){
                        imgContainer.append( '<div class="cont-thumb-img" data-url="'+v.url+'" style="background: url('+v.url+') no-repeat center center;background-size: cover;"></div>' );
                        attachmentids.push(v.id);
                    }
                })
                imgIdInput.val( attachmentids.toString() );
            });
            frame.on('open',function() {
                var selection = frame.state().get('selection');
                var ids = imgIdInput.val().split(',');
                ids.forEach(function(id) {
                    var attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                });
            });
            frame.open();
        });

        $('.admin-meta-box-fighter-images').on('click','.cont-thumb-img',function(e){
        	 e.stopPropagation();
        	$(this).siblings().css('overflow','hidden').empty();
        	$(this).css('overflow','visible').append('<div class="cont-img-preview"><img src="'+$(this).data('url')+'"></div>');
        });

        $('.admin-meta-box-fighter-images').on('click','.cont-img-preview img',function(e){
        	 e.stopPropagation();
        	$(this).parents('.cont-thumb-img').css('overflow','hidden').empty();
        });
        $('body').click(function(){
        	$(this).find('.cont-thumb-img').css('overflow','hidden').empty();
        });

		$('#btn-generate-fighters').click(function(){
            $('.logs-cont').empty();
            $('.progress').css('width',0);

			var cat = $('#dd-category').val();
            var limit = $('#inp-gen-limit').val();
            cur = 0;
            cat = cat.replace(' ','_');

			$.ajax({
				type: 'POST',
				url: ajax_auth_object.ajaxurl,
				data: {
					action: 'ajaxgeneratefighters',
                    cat: cat,
					limit: limit,
				},
				success: function(data){
					var pages = JSON.parse(data);
                    if(pages.length){
                        $('.cont-progress').show();
                        $('.logs-cont').html("0 of "+pages.length+" page/s fetched! <br>");
                        var get_fighterinterval = setInterval(function(){
                            if($.active==0){
                                if(cur==pages.length){
                                    clearInterval(get_fighterinterval);
                                }else{
                                    get_fighters(pages[cur],cat,pages.length);
                                }
                            }
                        },5000);
                    }
				},
				error: function(errorThrown){
				  	console.log(errorThrown);
				}
			});
		});
    });

    function update_progressbar(total_page,cur){
        $('.progress').css('width',($('.cont-progress').width()/total_page)*cur);
        if(cur==total_page){
            $('.cont-progress').fadeOut(500);
        }
    }

    function get_fighters(page,cat,total){
        // console.log(page);
        $.ajax({
            type: 'POST',
            url: ajax_auth_object.ajaxurl,
            data: {
                action: 'ajaxgeneratefightersperpage',
                cat: cat,
                page: page,
            },
            success: function(data){
                cur++;
                $('.logs-cont').html((cur)+" of "+total+" page/s fetched! <br>");
                update_progressbar(total,cur);
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
    }
}(jQuery));
