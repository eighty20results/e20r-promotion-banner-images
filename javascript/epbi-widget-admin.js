/** Ads Image Banner Widget scripts 
Plugin Name: Ads Image Banner Widget Plugin
Plugin URI: http://skmukhiya.com.np/ads-image-banner-widget-plugin
*/
jQuery(document).ready(function( $ ) {
	"use strict";

	// Handle the media library
	$(document).on('click', '.upload_image_button', function( e ) {

        e.preventDefault();

        var $button = $(this);
		var file_frame = wp.media.frames_file_frame = wp.media({
			title: 'Select or upload the image',
			library: {
				type: 'image'
			},
			button: {
				text: 'Select Image'
			},
			multiple: false
		});

        file_frame.on('select', function() {
        	// Since we disabled multiple, we'll only use the first selected entry
			var attachment = file_frame.state().get('selection').first().toJSON();

			$button.prev("input[type='text']").val( attachment.url );

            $button.closest('div.e20r-promotion-banner-settings').find('div.e20r-thumb img.e20r-embedded-img').attr('src', attachment.url );
		});

        file_frame.open();
	});

	$(".e20r-overlay").hide();

	$(".e20r-thumb").hover(function(){ $(this).find(".e20r-overlay").fadeIn(); }, function(){ $(this).find(".e20r-overlay").fadeOut(); });
	$("div[id*='_banner-']").addClass('e20r-banner-widget-set');
});
