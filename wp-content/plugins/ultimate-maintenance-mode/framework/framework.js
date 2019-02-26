// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) {
      arguments.callee = arguments.callee.caller;
      console.log( Array.prototype.slice.call(arguments) );
  }
};
// make it safe to use console.log always
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});

jQuery(document).ready(function($){
    // Uploader
  var _custom_media = true,
      _orig_send_attachment = wp.media.editor.send.attachment;

  $('.upload-button').click(function(e) {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    var id = jQuery(this).prev('input');
    _custom_media = true; 
    wp.media.editor.send.attachment = function(props, attachment){
      var size = props.size;
      var att =attachment.sizes[size];

      //props.size
      if ( _custom_media ) {
        $(id).val(att.url);
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
    

    
    // Color Picker
    $('.pickcolor').click( function(e) {
		colorPicker = jQuery(this).next('div');
		input = jQuery(this).prev('input');
		$(colorPicker).farbtastic(input);
		colorPicker.show();
		e.preventDefault();
		$(document).mousedown( function() {
    		$(colorPicker).hide();
    	});
	});
});

