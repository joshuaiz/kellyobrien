jQuery(document).ready( function($) {
	if( $('#wpfifc_taxonomies_edit_post_ID_id').length > 0 && $('#wpfifc_taxonomies_edit_term_ID_id').length > 0 ){					 
		$(".handlediv").click(function(){
			var p = $(this).parent('.postbox');
			
			p.toggleClass('closed');
		});
		
		WPSetThumbnailHTML = function(html){
			$('.inside', '#postimagediv').html(html);
		};
		
		WPRemoveThumbnail = function(nonce){
			var post_ID = 0;
			var term_ID = 0;
			
			if( $('#wpfifc_taxonomies_edit_post_ID_id').length > 0 ){
				post_ID = $('#wpfifc_taxonomies_edit_post_ID_id').val();
			}
			if( $('#wpfifc_taxonomies_edit_term_ID_id').length > 0 ){
				term_ID = $('#wpfifc_taxonomies_edit_term_ID_id').val();
			}
			if( post_ID < 1 || term_ID < 1 ){
				return;
			}
			$.post( ajaxurl, 
				{ action: "wpfifc-remove-image", 
				  post_id: post_ID, 
				  thumbnail_id: -1,
				  term_id: term_ID,
				  _ajax_nonce: nonce, 
				  cookie: encodeURIComponent(document.cookie)
				}, 
				function(str){
					if ( str.indexOf('ERROR') != '-1' ) {
						alert( "Remove featured image failed." );
					} else {
						WPSetThumbnailHTML(str);
					}
				}
			 );
		};
	}
});

function wpfifc_taxonomy_select_change( object ) {
	var select_id = object.id;
	var select_name = object.name;
	var instance_prefix = select_id.replace('wpfifc_term_widget_taxonomy', '');
	var option_val = jQuery("#" + select_id).val();
	var option_txt = jQuery("#" + select_id + " option:selected").text();
	var container_id = instance_prefix + 'wpfifc_term_widget_taxonomy_term';
	
	if (option_val < 1){
		return;
	}

	//use ajax to get all sorted
	var data = {
		action: 'wpfifcgetterms',
		taxonomy: option_val,
		prefix: instance_prefix
	};
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		jQuery("#" + container_id).html(response);
	});
}

