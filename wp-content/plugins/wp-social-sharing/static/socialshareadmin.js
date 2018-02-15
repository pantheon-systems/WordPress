jQuery(document).ready(function($){
	if ($('#set_custom_images').length > 0) {
		if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
	        jQuery('.wrap').on('click', '#set_custom_images', function(e) {
	            e.preventDefault();
	            var button = jQuery(this);
	            var id = button.prev();
	            wp.media.editor.send.attachment = function(props, attachment) {
	               id.val(attachment.url);
	               $('#set_custom_image_src').html('<img src="'+attachment.url+'" width="100px" />');
	            };
	            wp.media.editor.open(button);
	            return false;
	        });
		}
	}
	$("#remove_custom_images").on('click',function(){
		$('#pinterest_image').val('');
		$('#set_custom_image_src').html('');
	});
});