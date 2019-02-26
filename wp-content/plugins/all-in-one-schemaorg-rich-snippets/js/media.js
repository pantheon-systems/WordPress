jQuery(document).ready(function($){
	if(pagenow != 'attachment')
	{
	if (typeof wp === 'undefined' || typeof wp.media === 'undefined') return; 
	  var _custom_media = true,
		  _orig_send_attachment = wp.media.editor.send.attachment;
	  $('.bsf_upload_button').click(function(e) {
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		var id =  button.attr('id').replace('_id', '');
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
		  if ( _custom_media ) {
			$("."+id).val(attachment.url);
		$("#"+id+"_status").html('<img src="'+ attachment.url +'" />');
		  } else {
			return _orig_send_attachment.apply( this, [props, attachment] );
		  };
		}
		wp.media.editor.open(button);
		return false;
	  });
	  $('.add_media').on('click', function(){
		_custom_media = false;
	  });
	}
	else
	{
		$("#review_metabox").hide();
	}
});