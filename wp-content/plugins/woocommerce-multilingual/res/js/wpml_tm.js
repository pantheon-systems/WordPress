jQuery(document).ready(function($){
    var slug_checkbox = $('input[name="translate_slugs[product][on]"]');
    $('#icl_custom_posts_sync_options table').before( slug_checkbox.clone().attr('type','hidden').removeAttr('disabled') );

    slug_checkbox.attr('name','disabled');
    slug_checkbox.attr('disabled','disabled');
});

